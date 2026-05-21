/* global llcSettings */

/**
 * Settings page header with title, version and tab navigation.
 *
 * @param {Object} props          Component props.
 * @param {string} props.title    Page title.
 * @param {Object} props.children Tab navigation.
 */
const PageHeader = ({ title, children }) => {
	const version =
		(typeof llcSettings !== 'undefined' && llcSettings.version) || ''

	return (
		<div className="llc-page-header">
			<div className="llc-page-title">
				<h1>{title}</h1>
				{version && (
					<abbr className="llc-version" title={`Version: ${version}`}>
						{version}
					</abbr>
				)}
			</div>
			{children}
		</div>
	)
}

export default PageHeader
