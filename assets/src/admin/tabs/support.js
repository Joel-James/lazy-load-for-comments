import { __ } from '@wordpress/i18n'
import { Button, PanelBody } from '@wordpress/components'

// External support links.
const SUPPORT_LINKS = [
	{
		label: __('Documentation', 'lazy-load-for-comments'),
		icon: 'admin-page',
		href: 'https://docs.duckdev.com/lazy-load-for-comments/general',
	},
	{
		label: __('Support Forums', 'lazy-load-for-comments'),
		icon: 'groups',
		href: 'https://wordpress.org/support/plugin/lazy-load-for-comments/',
	},
	{
		label: __('Priority Support', 'lazy-load-for-comments'),
		icon: 'superhero',
		href: 'https://duckdev.com/contact/',
	},
]

// Author links.
const AUTHOR_LINKS = [
	{
		href: 'https://duckdev.com/about/',
		icon: 'admin-site',
		label: __('About Us', 'lazy-load-for-comments'),
	},
	{
		href: 'https://profiles.wordpress.org/joelcj91/',
		icon: 'wordpress',
		label: __('WP.org Profile', 'lazy-load-for-comments'),
	},
]

/**
 * Support tab — documentation, support forums and author info.
 */
const Support = () => (
	<>
		<PanelBody title={__('Support Information', 'lazy-load-for-comments')}>
			<p>
				{__(
					'Access our detailed documentation to handle most situations. For human feedback or community help, visit our wp.org forum. Premium customers receive priority support by contacting us directly.',
					'lazy-load-for-comments',
				)}
			</p>
			<div
				className="llc-button-group"
				style={{ display: 'flex', gap: '8px', marginTop: '15px' }}
			>
				{SUPPORT_LINKS.map((link) => (
					<Button
						key={link.href}
						variant="secondary"
						target="_blank"
						rel="noopener noreferrer"
						icon={link.icon}
						href={link.href}
					>
						{link.label}
					</Button>
				))}
			</div>
		</PanelBody>

		<PanelBody title={__('About the Author', 'lazy-load-for-comments')}>
			<p>
				{__(
					"Hey, I'm Joel James, a Software Engineer based in Kerala, India. I'm passionate about open source and dedicate a lot of my time to contributing to it. If you like this plugin, I'd encourage you to check out my other WordPress plugins as well!",
					'lazy-load-for-comments',
				)}
			</p>
			<div
				className="llc-button-group"
				style={{ display: 'flex', gap: '8px', marginTop: '15px' }}
			>
				{AUTHOR_LINKS.map((link) => (
					<Button
						key={link.href}
						variant="secondary"
						target="_blank"
						rel="noopener noreferrer"
						icon={link.icon}
						href={link.href}
					>
						{link.label}
					</Button>
				))}
			</div>
		</PanelBody>
	</>
)

export default Support
