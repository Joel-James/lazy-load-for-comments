import { __ } from '@wordpress/i18n'
import {
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
	TextControl,
} from '@wordpress/components'
import useSettings from '../use-settings'

/**
 * General settings tab — controls how and when comments are loaded.
 */
const General = () => {
	const { getSetting, setSetting } = useSettings()

	return (
		<PanelBody title={__('Loading Behaviour', 'lazy-load-for-comments')}>
			<PanelRow>
				<SelectControl
					__nextHasNoMarginBottom
					label={__('Load method', 'lazy-load-for-comments')}
					help={__(
						'Choose when the comments should be loaded on a post.',
						'lazy-load-for-comments',
					)}
					value={getSetting('load_method', 'scroll')}
					options={[
						{
							label: __('On scroll', 'lazy-load-for-comments'),
							value: 'scroll',
						},
						{
							label: __(
								'On button click',
								'lazy-load-for-comments',
							),
							value: 'click',
						},
						{
							label: __(
								'Disabled (load normally)',
								'lazy-load-for-comments',
							),
							value: 'off',
						},
					]}
					onChange={(value) => setSetting('load_method', value)}
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					__nextHasNoMarginBottom
					type="number"
					min={1}
					label={__('Minimum comments', 'lazy-load-for-comments')}
					help={__(
						'Only lazy load when the post has at least this many comments.',
						'lazy-load-for-comments',
					)}
					value={getSetting('minimum_count', 1)}
					onChange={(value) =>
						setSetting(
							'minimum_count',
							Math.max(1, parseInt(value, 10) || 1),
						)
					}
				/>
			</PanelRow>
			<PanelRow>
				<ToggleControl
					__nextHasNoMarginBottom
					label={__('Show loading spinner', 'lazy-load-for-comments')}
					help={__(
						'Display a spinner while the comments are being fetched.',
						'lazy-load-for-comments',
					)}
					checked={!!getSetting('show_loader', true)}
					onChange={(value) => setSetting('show_loader', value)}
				/>
			</PanelRow>
			<PanelRow>
				<ToggleControl
					__nextHasNoMarginBottom
					label={__(
						'Disable for search engines',
						'lazy-load-for-comments',
					)}
					help={__(
						'Load comments normally for bots and crawlers so they stay indexable.',
						'lazy-load-for-comments',
					)}
					checked={!!getSetting('disable_for_bots', true)}
					onChange={(value) => setSetting('disable_for_bots', value)}
				/>
			</PanelRow>
		</PanelBody>
	)
}

export default General
