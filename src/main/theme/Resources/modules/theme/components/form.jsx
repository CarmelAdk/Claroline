import React from 'react'
import {PropTypes as T} from 'prop-types'

import {trans} from '#/main/app/intl'
import {FormData} from '#/main/app/content/form/containers/data'
import {ThemePreview} from '#/main/theme/theme/components/preview'
import {Theme as ThemeTypes} from '#/main/theme/theme/prop-types'
import {connect} from 'react-redux'
import {actions as formActions, selectors as formSelectors} from '#/main/app/content/form/store'

const ThemeFormComponent = (props) =>
  <>
    <ThemePreview theme={props.theme} />
    <FormData
      name={props.name}
      buttons={true}
      target={(theme, isNew) => isNew ?
        ['apiv2_theme_create'] :
        ['apiv2_theme_update', {id: theme.id}]
      }
      definition={[
        {
          title: trans('branding'),
          fields: [
            {
              name: 'logo',
              type: 'image',
              label: trans('logo')
            }, {
              name: '_showTitle',
              type: 'boolean',
              label: trans('Ajouter un texte'),
              linked: [
                {
                  name: 'title',
                  type: 'string',
                  label: trans('name')
                }, {
                  name: 'subtitle',
                  type: 'string',
                  label: trans('secondary_name')
                }
              ]
            }
          ]
        }
      ]}
    />
  </>

ThemeFormComponent.propTypes = {
  className: T.string,
  name: T.string.isRequired,
  theme: T.shape(ThemeTypes.propTypes).isRequired
}

const ThemeForm = connect(
  (state, ownProps) => ({
    isNew: formSelectors.isNew(formSelectors.form(state, ownProps.name)),
    theme: formSelectors.data(formSelectors.form(state, ownProps.name))
  }),
  (dispatch) => ({
    save(theme, isNew, name) {
      return dispatch(formActions.saveForm(name, isNew ?
        ['apiv2_theme_create'] :
        ['apiv2_theme_update', {id: theme.id}])
      )
    }
  })
)(ThemeFormComponent)

export {
  ThemeForm
}