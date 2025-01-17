<?php

namespace Miraheze\MatomoAnalytics;

use MediaWiki\HTMLForm\OOUIHTMLForm;
use Xml;

class MatomoAnalyticsOOUIForm extends OOUIHTMLForm {
	/** @var bool */
	protected $mSubSectionBeforeFields = false;

	public function wrapForm( $html ) {
		$html = Xml::tags( 'div', [ 'id' => 'matomoanalytics' ], $html );

		return parent::wrapForm( $html );
	}

	protected function wrapFieldSetSection( $legend, $section, $attributes, $isRoot ) {
		$layout = parent::wrapFieldSetSection( $legend, $section, $attributes, $isRoot );

		$layout->addClasses( [ 'matomoanalytics-fieldset-wrapper' ] );
		$layout->removeClasses( [ 'oo-ui-panelLayout-framed' ] );

		return $layout;
	}

	public function getBody() {
		$tabPanels = [];
		foreach ( $this->mFieldTree as $key => $val ) {
			if ( !is_array( $val ) ) {
				wfDebug( __METHOD__ . " encountered a field not attached to a section: '{$key}'" );

				continue;
			}

			$label = $this->getLegend( $key );

			$content =
				$this->getHeaderHtml( $key ) .
				$this->displaySection(
					$val,
					'',
					"mw-section-{$key}-"
				) .
				$this->getFooterHtml( $key );

			$tabPanels[] = new \OOUI\TabPanelLayout( 'mw-section-' . $key, [
				'classes' => [ 'mw-htmlform-autoinfuse-lazy' ],
				'label' => $label,
				'content' => new \OOUI\FieldsetLayout( [
					'classes' => [ 'matomoanalytics-section-fieldset' ],
					'id' => "mw-section-{$key}",
					'label' => $label,
					'items' => [
						new \OOUI\Widget( [
							'content' => new \OOUI\HtmlSnippet( $content )
						] ),
					],
				] ),
				'expanded' => false,
				'framed' => true,
			] );
		}

		$indexLayout = new \OOUI\IndexLayout( [
			'infusable' => true,
			'expanded' => false,
			'autoFocus' => false,
			'classes' => [ 'matomoanalytics-tabs' ],
		] );

		$indexLayout->addTabPanels( $tabPanels );

		$header = $this->formatFormHeader();

		$form = new \OOUI\PanelLayout( [
			'framed' => true,
			'expanded' => false,
			'classes' => [ 'matomoanalytics-tabs-wrapper' ],
			'content' => $indexLayout
		] );

		return $header . $form;
	}
}
