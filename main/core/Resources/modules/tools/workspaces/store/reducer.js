import {makeInstanceAction} from '#/main/app/store/actions'
import {combineReducers, makeReducer} from '#/main/app/store/reducer'
import {makeListReducer} from '#/main/app/content/list/store/reducer'
import {makeFormReducer} from '#/main/app/content/form/store/reducer'

import {TOOL_LOAD} from '#/main/core/tool/store/actions'

import {WORKSPACE_CREATION_LOG} from '#/main/core/tools/workspaces/store/actions'
import {Workspace} from '#/main/core/workspace/prop-types'

export const reducer = combineReducers({
  /**
   * Current configuration of the tool.
   */
  parameters: makeReducer({}),

  /**
   * Does the current user can create new workspaces ?
   */
  creatable: makeReducer(false, {
    [makeInstanceAction(TOOL_LOAD, 'workspaces')]: (state, action) => action.toolData.creatable
  }),

  /**
   * The form to create new workspaces.
   */
  creation: makeFormReducer('workspaces.creation', {
    new: true,
    data: Workspace.defaultProps,
    originalData: Workspace.defaultProps
  }, {
    logs: makeReducer({}, {
      [WORKSPACE_CREATION_LOG]: (state, action) => {
        try {
          return JSON.parse(action.content)
        } catch (e) {
          return {}
        }
      }
    })
  }),

  /**
   * The list of workspaces in which the current user is registered.
   */
  registered: makeListReducer('workspaces.registered', {
    sortBy: {property: 'created', direction: -1}
  }, {
    invalidated: makeReducer(false, {
      [makeInstanceAction(TOOL_LOAD, 'workspaces')]: () => true
    })
  }),

  /**
   * The list of the platform public workspaces.
   */
  public: makeListReducer('workspaces.public', {
    sortBy: {property: 'created', direction: -1}
  }, {
    invalidated: makeReducer(false, {
      [makeInstanceAction(TOOL_LOAD, 'workspaces')]: () => true
    })
  }),

  /**
   * The list of workspaces managed by the current user.
   */
  managed: makeListReducer('workspaces.managed', {
    filters: [
      {property: 'meta.personal', value: false},
      {property: 'meta.model', value: false}
    ],
    sortBy: {property: 'created', direction: -1}
  }, {
    invalidated: makeReducer(false, {
      [makeInstanceAction(TOOL_LOAD, 'workspaces')]: () => true
    })
  }),

  /**
   * The list of the platform public workspaces.
   */
  models: makeListReducer('workspaces.models', {
    sortBy: {property: 'created', direction: -1}
  }, {
    invalidated: makeReducer(false, {
      [makeInstanceAction(TOOL_LOAD, 'workspaces')]: () => true
    })
  })
})
