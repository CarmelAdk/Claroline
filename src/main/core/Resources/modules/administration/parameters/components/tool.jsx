import React from 'react'
import {PropTypes as T} from 'prop-types'

import {Routes} from '#/main/app/router'

import {Meta} from '#/main/core/administration/parameters/main/containers/meta'
import {Technical} from '#/main/core/administration/parameters/technical/containers/technical'

import {AppearanceTool} from '#/main/theme/administration/appearance/containers/tool'

const ParametersTool = (props) =>
  <Routes
    path={props.path}
    routes={[
      {
        path: '/',
        exact: true,
        component: Meta
      },
      {
        path: '/technical',
        component: Technical
      }, {
        path: '/appearance',
        component: AppearanceTool
      }
    ]}
  />

ParametersTool.propTypes = {
  path: T.string
}

export {
  ParametersTool
}
