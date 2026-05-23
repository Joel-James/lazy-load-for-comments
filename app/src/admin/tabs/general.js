/* global llcSettings */
import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import { useDispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import {
	Button,
	Notice,
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
	TextControl,
} from '@wordpress/components'
import useSettings from '../use-settings'

/**
 * General settings tab — loading behaviour + cache management.
 */
const General = () => {
	const { getSetting, setSetting } = useSettings()
	const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore)
	const [isClearing, setIsClearing] = useState(false)
	const isClick = getSetting('load_method', 'scroll') === 'click'

	const clearCache = async () => {
		setIsClearing(true)

		try {
			const response = await fetch(`${llcSettings.restUrl}cache`, {
				method: 'DELETE',
				credentials: 'same-origin',
				headers: {
					Accept: 'application/json',
					'X-WP-Nonce': llcSettings.restNonce,
				},
			})

			if (!response.ok) {
				throw new Error('Request failed')
			}

			const data = await response.json()

			createSuccessNotice(
				data.message ||
					__('Comments cache cleared.', 'lazy-load-for-comments'),
				{ type: 'snackbar' },
			)
		} catch (e) {
			createErrorNotice(
				__('Could not clear the cache.', 'lazy-load-for-comments'),
				{ type: 'snackbar' },
			)
		} finally {
			setIsClearing(false)
		}
	}

	return (
		<>
			<PanelBody
				title={__('Loading Behaviour', 'lazy-load-for-comments')}
			>
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
								label: __(
									'On scroll',
									'lazy-load-for-comments',
								),
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
						label={__(
							'Minimum comments',
							'lazy-load-for-comments',
						)}
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
						label={__(
							'Show loading spinner',
							'lazy-load-for-comments',
						)}
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
						onChange={(value) =>
							setSetting('disable_for_bots', value)
						}
					/>
				</PanelRow>
			</PanelBody>

			<PanelBody
				title={__('Load Button', 'lazy-load-for-comments')}
			>
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
					<SelectControl
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
								label: __(
									'Plugin style',
									'lazy-load-for-comments',
								),
								value: 'custom',
							},
						]}
						onChange={(value) => setSetting('button_style', value)}
					/>
				</PanelRow>
				<PanelRow>
					<TextControl
						__nextHasNoMarginBottom
						label={__(
							'Extra CSS classes',
							'lazy-load-for-comments',
						)}
						help={__(
							'Space separated CSS classes added to the button, e.g. to match your theme.',
							'lazy-load-for-comments',
						)}
						value={getSetting('button_class', '')}
						onChange={(value) => setSetting('button_class', value)}
					/>
				</PanelRow>
			</PanelBody>

			<PanelBody
				title={__('Cache', 'lazy-load-for-comments')}
			>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__(
							'Store comments markup in cache',
							'lazy-load-for-comments',
						)}
						help={__(
							'Cache the parsed comments block in a transient so the REST endpoint can render it quickly. Turn this off if you are debugging or your comments markup changes frequently.',
							'lazy-load-for-comments',
						)}
						checked={!!getSetting('cache_enabled', true)}
						onChange={(value) => setSetting('cache_enabled', value)}
					/>
				</PanelRow>
				<PanelRow>
					<div>
						<p>
							{__(
								'Clear the cached comments markup for every post. Useful after editing your theme templates or comment-related blocks.',
								'lazy-load-for-comments',
							)}
						</p>
						<Button
							variant="secondary"
							onClick={clearCache}
							isBusy={isClearing}
							disabled={isClearing}
						>
							{isClearing
								? __('Clearing…', 'lazy-load-for-comments')
								: __(
										'Clear comments cache',
										'lazy-load-for-comments',
									)}
						</Button>
					</div>
				</PanelRow>
			</PanelBody>
		</>
	)
}

export default General
