/* global llcSettings */
import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import { useDispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import {
	Button,
	PanelBody,
	PanelRow,
	ToggleControl,
} from '@wordpress/components'
import useSettings from '../../hooks/use-settings'

const Cache = () => {
	const { getSetting, setSetting } = useSettings()
	const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore)
	const [isClearing, setIsClearing] = useState(false)

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
		<PanelBody title={__('Cache', 'lazy-load-for-comments')}>
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
						__next40pxDefaultSize
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
	)
}

export default Cache
