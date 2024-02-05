import {connect} from 'react-redux'
import get from 'lodash/get'

import {selectors as paramSelectors} from '#/main/core/administration/parameters/store'
import {selectors} from '#/main/theme/administration/appearance/store'

import {AppearanceThemes as AppearanceThemesComponent} from '#/main/theme/administration/appearance/components/themes'

const AppearanceThemes = connect(
  (state) => ({
    currentTheme: get(paramSelectors.parameters(state), 'display.theme'),
    availableThemes: selectors.availableThemes(state)
  })
)(AppearanceThemesComponent)

export {
  AppearanceThemes
}
