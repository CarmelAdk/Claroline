import React from 'react'
import classes from 'classnames'
import merge from 'lodash/merge'
import omit from 'lodash/omit'

import {implementPropTypes} from '#/main/app/prop-types'
import {PageFull as PageFullTypes} from '#/main/app/page/prop-types'
import {PageSimple} from '#/main/app/page/components/simple'
import {PageHeader} from '#/main/app/page/components/header'

const PageFull = (props) =>
  <PageSimple
    {...omit(props, 'showHeader', 'showTitle', 'header', 'title', 'subtitle', 'icon', 'poster', 'toolbar', 'actions', 'nav')}
    meta={merge({}, {
      title: props.title,
      poster: props.poster
    }, props.meta || {})}
  >
    <div className="app-loader" />

    {props.showHeader &&
      <PageHeader
        id={props.id}
        showTitle={props.showTitle}
        showBreadcrumb={props.showBreadcrumb}
        path={props.path}
        title={props.title}
        subtitle={props.subtitle}
        icon={props.icon}
        poster={props.poster}
        toolbar={props.toolbar}
        disabled={props.disabled}
        primaryAction={props.primaryAction}
        actions={props.actions}
        nav={props.nav}
      >
        {props.header}
      </PageHeader>
    }

    <div role="presentation" className={classes('page-content container-fluid', {'main-page-content': !props.embedded})}>
      {props.children}
    </div>
  </PageSimple>

implementPropTypes(PageFull, PageFullTypes)

export {
  PageFull
}
