import { __ } from '@wordpress/i18n'
import {
	Notice,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
} from '@wordpress/components'
import useSettings from '../../hooks/use-settings'

const LoadButton = () => {
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
					__next40pxDefaultSize
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
				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Button style', 'lazy-load-for-comments')}
					help={__(
						'Inherit your theme button style, or use the plugin built-in style.',
						'lazy-load-for-comments',
					)}
					value={getSetting('button_style', 'theme')}
					options={[
						{
							label: __(
								'Inherit theme style',
								'lazy-load-for-comments',
							),
							value: 'theme',
						},
						{
							label: __('Plugin style', 'lazy-load-for-comments'),
							value: 'custom',
						},
					]}
					onChange={(value) => setSetting('button_style', value)}
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					__next40pxDefaultSize
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

export default LoadButton
