import clsx from 'clsx'

/**
 * Tab navigation for the settings page.
 *
 * @param {Object}   props          Component props.
 * @param {string}   props.current  Current tab key.
 * @param {Object}   props.navs     Map of tab key => label.
 * @param {Function} props.onChange Tab change handler.
 */
const TabNav = ({ current, navs, onChange }) => (
	<nav
		className="llc-tabs"
		style={{
			gridTemplateColumns: Object.keys(navs)
				.map(() => '1fr')
				.join(' '),
		}}
	>
		{Object.keys(navs).map((key) => (
			<a
				key={key}
				href={`#${key}`}
				className={clsx('llc-tab', { active: key === current })}
				onClick={(e) => {
					e.preventDefault()
					onChange(key)
				}}
			>
				{navs[key]}
			</a>
		))}
	</nav>
)

export default TabNav
