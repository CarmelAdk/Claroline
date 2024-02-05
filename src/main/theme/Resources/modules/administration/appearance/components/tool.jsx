import React from 'react'
import {PropTypes as T} from 'prop-types'

import {Routes} from '#/main/app/router'
import {AppearanceParameters} from '#/main/theme/administration/appearance/containers/parameters'
import {AppearanceTheme} from '#/main/theme/administration/appearance/components/theme'

const AppearanceTool = (props) =>
  <Routes
    path={props.path+'/appearance'}
    routes={[
      {
        path: '',
        exact: true,
        component: AppearanceParameters
      }, {
        path: '/theme/:id',
        render: (routeProps) => {
          const theme = props.availableThemes.find(theme => theme.id === routeProps.match.params.id)
          console.log(theme)
          if (theme) {
            return (
              <AppearanceTheme path={props.path} theme={theme} />
            )
          }

          routeProps.history.push(props.path+'/appearance')

          return null
        }
      }
    ]}
  />

AppearanceTool.propTypes = {
  path: T.string.isRequired,
  availableThemes: T.array
}

AppearanceTool.defaultProps = {
  availableThemes: []
}

export {
  AppearanceTool
}
