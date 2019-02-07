import {bootstrap} from '#/main/app/dom/bootstrap'

import {reducer} from '#/main/core/tools/contact/store/reducer'
import {Tool} from '#/main/core/tools/contact/components/tool'

// mount the react application
bootstrap(
  // app DOM container (also holds initial app data as data attributes)
  '.contacts-container',

  // app main component
  Tool,

  // app store configuration
  reducer,

  (initialData) => ({
    options: {
      data: initialData.options,
      originalData: initialData.options
    }
  })
)