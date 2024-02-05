import {PropTypes as T} from 'prop-types'

const Theme = {
  propTypes: {
    id: T.string,
    name: T.string,
    normalizedName: T.string,

    logo: T.string,
    title: T.string,
    subtitle: T.string
  },
  defaultProps: {

  }
}

export {
  Theme
}
