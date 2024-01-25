import React from 'react'
import {PropTypes as T} from 'prop-types'

import {Button} from '#/main/app/action/components/button'

const PageNav = (props) =>
  <nav className="page-nav ms-auto">
    {props.children}
    <ul className="nav nav-underline">
      {props.links.map((link) =>
        <li className="nav-item">
          <Button
            {...link}
            className="nav-link"
            tooltip={'configure' === link.name ? 'bottom' : undefined}
          />
        </li>
      )}
    </ul>
  </nav>

PageNav.propTypes = {
  links: T.arrayOf(T.shape({

  }))
}

export {
  PageNav
}
