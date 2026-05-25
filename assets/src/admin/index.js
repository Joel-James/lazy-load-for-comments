import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import { Placeholder, Spinner } from '@wordpress/components'
import useSettings from './hooks/use-settings'
import tabs from './tabs'
import PageHeader from './components/page-header'
import PageBody from './components/page-body'
import TabNav from './components/tab-nav'
import Footer from './components/footer'
import Notices from './components/notices'

/**
 * Root settings page component.
 */
const App = () => {
	const { hasLoaded } = useSettings()

	// First registered tab is the landing tab, so renames in `tabs/index.js`
	// don't silently break the default state here.
	const tabKeys = Object.keys(tabs)
	const defaultKey = tabKeys[0]

	const [current, setCurrent] = useState(defaultKey)

	const navs = Object.fromEntries(
		Object.entries(tabs).map(([key, tab]) => [key, tab.label]),
	)

	const ActiveTab = (tabs[current] || tabs[defaultKey]).component

	return (
		<>
			<PageHeader
				title={__('Lazy Load for Comments', 'lazy-load-for-comments')}
			>
				<TabNav current={current} navs={navs} onChange={setCurrent} />
			</PageHeader>

			<PageBody>
				{!hasLoaded ? (
					<Placeholder>
						<Spinner />
					</Placeholder>
				) : (
					<>
						<ActiveTab />
						<Footer />
					</>
				)}
			</PageBody>

			<Notices />
		</>
	)
}

export default App
