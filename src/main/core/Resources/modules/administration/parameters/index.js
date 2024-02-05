
import {reducer} from '#/main/core/administration/parameters/store/reducer'
import {ParametersTool} from '#/main/core/administration/parameters/components/tool'
import {ParametersMenu} from '#/main/core/administration/parameters/components/menu'

export default {
  component: ParametersTool,
  menu: ParametersMenu,
  store: reducer
}
