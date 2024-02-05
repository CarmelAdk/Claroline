import React from 'react'
import {PropTypes as T} from 'prop-types'
import {asset} from '#/main/app/config'

// icon colors

const ThemePreview = (props) =>
  <div className="theme-preview">
    <div className="layout">
      <div className="layout-header">
        <div className="layout-brand">
          {props.logo &&
            <img className="layout-logo" src={asset(props.logo)} alt="logo"/>
          }

          {props.showTitle && props.title &&
            <h1 className="app-header-title d-none d-md-block">
              {props.title}

              {props.subtitle &&
                <small>{props.subtitle}</small>
              }
            </h1>
          }
        </div>
      </div>
    </div>
    <div className="color-preview">
      <div className="color-preview-menu text-bg-menu">Menu</div>
      <div className="color-preview-primary text-bg-primary">Primary</div>
      <div className="color-preview-menu text-bg-learning">Secondary</div>
    </div>
    <div className="color-preview-status">
      <div className="color-preview-success text-bg-success">Success</div>
      <div className="color-preview-danger text-bg-danger">Danger</div>
      <div className="color-preview-warning text-bg-warning">Warning</div>
      <div className="color-preview-info text-bg-info">Info</div>
    </div>

    <div className="color-preview-rainbow">

    </div>
  </div>

ThemePreview.propTypes = {
  logo: T.string,
  showTitle: T.bool,
  title: T.bool,
  subtitle: T.string
}

export {
  ThemePreview
}
