import React from 'react'
import {PropTypes as T} from 'prop-types'

import {trans} from '#/main/app/intl/translation'
import {ContentPlaceholder} from '#/main/app/content/components/placeholder'

import {Role as RoleTypes} from '#/main/community/prop-types'
import {RoleCard} from '#/main/community/role/components/card'

const RoleDisplay = (props) => props.data ?
  <RoleCard
    data={props.data}
    size="xs"
  /> :
  <ContentPlaceholder
    icon="fa fa-fw fa-id-badge"
    title={trans('no_role')}
  />

RoleDisplay.propTypes = {
  data: T.arrayOf(T.shape(
    RoleTypes.propTypes
  ))
}

export {
  RoleDisplay
}
