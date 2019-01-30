import React from 'react'
import {PropTypes as T} from 'prop-types'
import merge from 'lodash/merge'

import {trans} from '#/main/app/intl/translation'
import {ToolPage} from '#/main/core/tool/containers/page'

import {ListSource} from '#/main/app/content/list/containers/source'
import {constants} from '#/main/app/content/list/constants'
import {createListDefinition} from '#/main/app/content/list/utils'

import resourcesSource from '#/main/core/data/sources/resources'
import deleteAction from '#/main/core/resource/actions/delete'
import restoreAction from '#/main/core/resource/actions/restore'

const TrashTool = props => {
  const definition = createListDefinition(resourcesSource.parameters.definition)

  const nodesRefresher = {
    update: props.invalidate,
    delete: props.invalidate
  }

  return (
    <ToolPage
      subtitle={trans('trash')}
    >
      <ListSource
        name="resources"
        fetch={{
          url: ['apiv2_resource_workspace_removed_list', {
            workspace: props.workspace.uuid
          }],
          autoload: true
        }}
        source={merge({}, resourcesSource, {
          // adds actions to source
          parameters: {
            actions: (resourceNodes) => [
              // we just expose delete actions
              deleteAction(resourceNodes, nodesRefresher),
              restoreAction(resourceNodes, nodesRefresher)
            ]
          }
        })}
        parameters={{
          // For now we don't allow to customize the trash resource list
          // So we just get the defaults from source
          display: constants.DISPLAY_TILES_SM,
          actions: true,
          columns: definition.filter(column => column.displayed).map(column => column.name),
          availableColumns: definition.filter(column => column.displayable).map(column => column.name),
          availableFilters: definition.filter(column => column.filterable).map(column => column.name),
          availableSort: definition.filter(column => column.sortable).map(column => column.name)
        }}
      />
    </ToolPage>
  )
}

TrashTool.propTypes = {
  workspace: T.shape({
    uuid: T.string.isRequired
  }),
  invalidate: T.func.isRequired
}

export {
  TrashTool
}
