import { __ } from '@wordpress/i18n'
import { Notice, PanelBody, PanelRow, TextControl } from '@wordpress/components'
import useSettings from '../use-settings'

/**
 * Button settings tab — customises the "Load Comments" button.
 *
 * These options only apply when the load method is "On button click".
 */
const ButtonTab = () => {
	const { getSetting, setSetting } = useSettings()
	const isClick = getSetting('load_method', 'scroll') === 'click'

	return (
		<PanelBody title={__('Load Button', 'lazy-load-for-comments')}>
			{!isClick && (
				<PanelRow>
					<Notice status="info" isDismissible={false}>
						{__(
							'These options apply only when the load method is set to "On button click".',
							'lazy-load-for-comments',
						)}
					</Notice>
				</PanelRow>
			)}
			<PanelRow>
				<TextControl
					__nextHasNoMarginBottom
					label={__('Button text', 'lazy-load-for-comments')}
					help={__(
						'Text shown on the button visitors click to load comments.',
						'lazy-load-for-comments',
					)}
					value={getSetting('button_text', '')}
					onChange={(value) => setSetting('button_text', value)}
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					__nextHasNoMarginBottom
					label={__('Extra CSS classes', 'lazy-load-for-comments')}
					help={__(
						'Space separated CSS classes added to the button, e.g. to match your theme.',
						'lazy-load-for-comments',
					)}
					value={getSetting('button_class', '')}
					onChange={(value) => setSetting('button_class', value)}
				/>
			</PanelRow>
		</PanelBody>
	)
}

export default ButtonTab
