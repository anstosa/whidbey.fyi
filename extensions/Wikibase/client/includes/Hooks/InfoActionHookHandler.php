<?php

namespace Wikibase\Client\Hooks;

use Html;
use IContextSource;
use MediaWiki\Hook\InfoActionHook;
use Title;
use Wikibase\Client\NamespaceChecker;
use Wikibase\Client\RepoLinker;
use Wikibase\Client\Store\DescriptionLookup;
use Wikibase\Client\Usage\UsageLookup;
use Wikibase\Client\WikibaseClient;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\Lib\Store\LanguageFallbackLabelDescriptionLookupFactory;
use Wikibase\Lib\Store\SiteLinkLookup;

/**
 * @license GPL-2.0-or-later
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class InfoActionHookHandler implements InfoActionHook {

	/**
	 * @var NamespaceChecker
	 */
	private $namespaceChecker;

	/**
	 * @var RepoLinker
	 */
	private $repoLinker;

	/**
	 * @var SiteLinkLookup
	 */
	private $siteLinkLookup;

	/**
	 * @var string
	 */
	private $siteId;

	/**
	 * @var UsageLookup
	 */
	private $usageLookup;

	/**
	 * @var LanguageFallbackLabelDescriptionLookupFactory
	 */
	private $labelDescriptionLookupFactory;

	/**
	 * @var EntityIdParser
	 */
	private $idParser;

	/**
	 * @var DescriptionLookup
	 */
	private $descriptionLookup;

	public function __construct(
		NamespaceChecker $namespaceChecker,
		RepoLinker $repoLinker,
		SiteLinkLookup $siteLinkLookup,
		$siteId,
		UsageLookup $usageLookup,
		LanguageFallbackLabelDescriptionLookupFactory $labelDescriptionLookupFactory,
		EntityIdParser $idParser,
		DescriptionLookup $descriptionLookup
	) {
		$this->namespaceChecker = $namespaceChecker;
		$this->repoLinker = $repoLinker;
		$this->siteLinkLookup = $siteLinkLookup;
		$this->siteId = $siteId;
		$this->usageLookup = $usageLookup;
		$this->labelDescriptionLookupFactory = $labelDescriptionLookupFactory;
		$this->idParser = $idParser;
		$this->descriptionLookup = $descriptionLookup;
	}

	public static function factory(): self {
		$wikibaseClient = WikibaseClient::getDefaultInstance();
		$settings = $wikibaseClient->getSettings();

		$namespaceChecker = $wikibaseClient->getNamespaceChecker();
		$usageLookup = $wikibaseClient->getStore()->getUsageLookup();
		$labelDescriptionLookupFactory = new LanguageFallbackLabelDescriptionLookupFactory(
			$wikibaseClient->getLanguageFallbackChainFactory(),
			$wikibaseClient->getTermLookup(),
			$wikibaseClient->getTermBuffer()
		);
		$idParser = $wikibaseClient->getEntityIdParser();
		$descriptionLookup = $wikibaseClient->getDescriptionLookup();

		return new self(
			$namespaceChecker,
			$wikibaseClient->newRepoLinker(),
			$wikibaseClient->getStore()->getSiteLinkLookup(),
			$settings->getSetting( 'siteGlobalID' ),
			$usageLookup,
			$labelDescriptionLookupFactory,
			$idParser,
			$descriptionLookup
		);
	}

	/**
	 * Adds the Entity ID of the corresponding Wikidata item in action=info
	 *
	 * @param IContextSource $context
	 * @param array[] &$pageInfo
	 */
	public function onInfoAction( $context, &$pageInfo ) {
		// Check if wikibase namespace is enabled
		$title = $context->getTitle();
		$usage = $this->usageLookup->getUsagesForPage( $title->getArticleID() );
		$localDescription = $this->descriptionLookup->getDescription( $title,
			DescriptionLookup::SOURCE_LOCAL );
		$centralDescription = $this->descriptionLookup->getDescription( $title,
			DescriptionLookup::SOURCE_CENTRAL );

		if ( $this->namespaceChecker->isWikibaseEnabled( $title->getNamespace() ) && $title->exists() ) {
			$pageInfo['header-basic'][] = $this->getPageInfoRow( $context, $title );
		}
		if ( $localDescription ) {
			$pageInfo['header-basic'][] = $this->getDescriptionInfoRow( $context, $localDescription,
				DescriptionLookup::SOURCE_LOCAL );
		}
		if ( $centralDescription ) {
			$pageInfo['header-basic'][] = $this->getDescriptionInfoRow( $context, $centralDescription,
				DescriptionLookup::SOURCE_CENTRAL );
		}

		if ( $usage ) {
			$pageInfo['header-properties'][] = $this->formatEntityUsage( $context, $usage );
		}
	}

	/**
	 * @param IContextSource $context
	 * @param Title $title
	 *
	 * @return string[]
	 */
	private function getPageInfoRow( IContextSource $context, Title $title ) {
		$entityId = $this->siteLinkLookup->getItemIdForLink(
			$this->siteId,
			$title->getPrefixedText()
		);

		$row = $entityId ? $this->getItemPageInfo( $context, $entityId )
			: $this->getUnconnectedItemPageInfo( $context );

		return $row;
	}

	/**
	 * @param IContextSource $context
	 * @param string $description
	 * @param string $source
	 *
	 * @return string[]
	 */
	private function getDescriptionInfoRow( $context, $description, $source ) {
		return [
			// messages: wikibase-pageinfo-description-local, wikibase-pageinfo-description-central
			$context->msg( 'wikibase-pageinfo-description-' . $source )->parse(),
			$description
		];
	}

	/**
	 * Creating a Repo link with Item ID as anchor text
	 *
	 * @param IContextSource $context
	 * @param ItemId $itemId
	 *
	 * @return string[]
	 */
	private function getItemPageInfo( IContextSource $context, ItemId $itemId ) {
		$itemLink = $this->repoLinker->buildEntityLink(
			$itemId,
			[ 'external' ]
		);

		return [
			$context->msg( 'wikibase-pageinfo-entity-id' )->parse(),
			$itemLink
		];
	}

	/**
	 * @param IContextSource $context
	 *
	 * @return string[]
	 */
	private function getUnconnectedItemPageInfo( IContextSource $context ) {
		return [
			$context->msg( 'wikibase-pageinfo-entity-id' )->parse(),
			$context->msg( 'wikibase-pageinfo-entity-id-none' )->parse()
		];
	}

	/**
	 * @param string[][] $aspects
	 * @param IContextSource $context
	 * @return string
	 */
	private function formatAspects( array $aspects, IContextSource $context ) {
		$aspectContent = '';
		foreach ( $aspects as $aspect ) {
			// Possible messages:
			//   wikibase-pageinfo-entity-usage-L
			//   wikibase-pageinfo-entity-usage-L-with-modifier
			//   wikibase-pageinfo-entity-usage-D
			//   wikibase-pageinfo-entity-usage-D-with-modifier
			//   wikibase-pageinfo-entity-usage-C
			//   wikibase-pageinfo-entity-usage-C-with-modifier
			//   wikibase-pageinfo-entity-usage-S
			//   wikibase-pageinfo-entity-usage-T
			//   wikibase-pageinfo-entity-usage-X
			//   wikibase-pageinfo-entity-usage-O
			$msgKey = 'wikibase-pageinfo-entity-usage-' . $aspect[0];
			if ( $aspect[1] !== null ) {
				$msgKey .= '-with-modifier';
			}
			$aspectContent .= Html::rawElement(
				'li',
				[],
				$context->msg( $msgKey, $aspect[1] )->parse()
			);
		}
		return $aspectContent;
	}

	/**
	 * @param IContextSource $context
	 * @param array $usage
	 *
	 * @return string[]
	 */
	private function formatEntityUsage( IContextSource $context, array $usage ) {
		$usageAspectsByEntity = [];
		$entities = [];
		foreach ( $usage as $entityUsage ) {
			$entityId = $entityUsage->getEntityId()->getSerialization();
			$entities[$entityId] = $entityUsage->getEntityId();
			$usageAspectsByEntity[$entityId][] = [
				$entityUsage->getAspect(),
				$entityUsage->getModifier()
			];
		}
		$output = '';
		$entityIds = array_map(
			function( $entityId ) {
				return $this->idParser->parse( $entityId );
			},
			array_keys( $usageAspectsByEntity )
		);
		$labelLookup = $this->labelDescriptionLookupFactory->newLabelDescriptionLookup(
			$context->getLanguage(),
			$entityIds
		);
		foreach ( $usageAspectsByEntity as $entityId => $aspects ) {
			$label = $labelLookup->getLabel( $this->idParser->parse( $entityId ) );
			$text = $label === null ? $entityId : $label->getText();

			$output .= Html::rawElement( 'li', [],
				$this->repoLinker->buildEntityLink(
					$entities[$entityId],
					[ 'external' ],
					$text
				)
			);

			$aspectContent = $this->formatAspects( $aspects, $context );
			$output .= Html::rawElement( 'ul', [], $aspectContent );
		}
		$output = Html::rawElement( 'ul', [], $output );
		return [ $context->msg( 'wikibase-pageinfo-entity-usage' )->parse(), $output ];
	}

}
