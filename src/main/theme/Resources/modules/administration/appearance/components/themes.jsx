import React from 'react'
import {PropTypes as T} from 'prop-types'

import {trans} from '#/main/app/intl'
import {Button} from '#/main/app/action'
import {LINK_BUTTON} from '#/main/app/buttons'

import {Theme as ThemeTypes} from '#/main/theme/theme/prop-types'
import {ThemePreview} from '#/main/theme/theme/components/preview'

const AppearanceThemes = (props) => {
  const current = props.availableThemes.find(theme => theme.normalizedName === props.currentTheme)

  return (
    <div>
      {current.name}
      <ThemePreview {...current} />
      <Button
        className="btn btn-outline-primary mt-3"
        type={LINK_BUTTON}
        label={trans('edit', {}, 'actions')}
        target={props.path+'/appearance/theme/'+current.id}
      />
    </div>
  )
}

AppearanceThemes.propTypes = {
  path: T.string.isRequired,
  currentTheme: T.string,
  availableThemes: T.arrayOf(T.shape(
    ThemeTypes.propTypes
  ))
}

export {
  AppearanceThemes
}
