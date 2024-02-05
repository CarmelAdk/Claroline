import React from 'react'
import {PropTypes as T} from 'prop-types'

import {trans} from '#/main/app/intl'
import {LINK_BUTTON} from '#/main/app/buttons'
import {ToolPage} from '#/main/core/tool/containers/page'

import {Theme as ThemeTypes} from '#/main/theme/theme/prop-types'
import {ThemeForm} from '#/main/theme/theme/components/form'
import {selectors} from '#/main/theme/administration/appearance/store'

const AppearanceTheme = (props) =>
  <ToolPage
    path={[
      {
        type: LINK_BUTTON,
        label: trans('appearance'),
        target: `${props.path}/appearance`
      }, {
        type: LINK_BUTTON,
        label: props.theme.name,
        target: '' // current page
      }
    ]}
    subtitle={trans('theme_name', {name: props.theme.name}, 'appearance')}
  >
    <ThemeForm
      className="mt-3"
      path={props.path}
      name={selectors.THEME_FORM_NAME}
    />
  </ToolPage>

AppearanceTheme.propTypes = {
  path: T.string.isRequired,
  theme: T.shape(ThemeTypes.propTypes).isRequired
}

export {
  AppearanceTheme
}