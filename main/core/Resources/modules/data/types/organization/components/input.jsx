import React, {Component} from 'react'
import pickBy from 'lodash/pickBy'
import {PropTypes as T, implementPropTypes} from '#/main/app/prop-types'

import {trans} from '#/main/app/intl/translation'
import {url, makeCancelable} from '#/main/app/api'

import {FormField as FormFieldTypes} from '#/main/core/layout/form/prop-types'

import {Select} from '#/main/core/layout/form/components/field/select'

class OrganizationInput extends Component {
  constructor(props) {
    super(props)

    this.state = {
      fetched: false,
      organizations: []
    }

    // retrieve locales
    this.fetchOrganizations()
  }

  fetchOrganizations() {
    this.pending = makeCancelable(
      fetch(
        url(['apiv2_organization_list']), {credentials: 'include'}
      )
        .then(response => response.json())
        .then(
          (data) => {
            this.loadOrganizations(data.data)
            this.pending = null
          },
          () => this.pending = null
        )
    )
  }

  loadOrganizations(organizations) {
    this.setState({
      fetched: true,
      organizations: organizations
    })
  }

  componentWillUnmount() {
    if (this.pending) {
      this.pending.cancel()
    }
  }

  render() {
    if (!this.state.fetched) {
      return (
        <div>
          {trans('Please wait while we load organizations...')}
          </div>
      )
    }

    return (
      <Select
        id={this.props.id}
        choices={pickBy(this.state.organizations.reduce((choices, organization) => {
          choices[organization.id] = organization.name

          return choices
        }, {}), this.props.filterChoices)}
        value={this.props.value ? this.props.value.id : ''}
        onChange={(value) => this.props.onChange({
          id: value,
          name: this.state.organizations.find(organization => value === organization.id).name
        })}
      />
    )
  }
}

implementPropTypes(OrganizationInput, FormFieldTypes, {
  // more precise value type
  value: T.shape({
    id: T.string.isRequired,
    name: T.string.isRequired
  }),
  filterChoices: T.func
}, {
  label: trans('organization'),
  filterChoices: () => true
})

export {
  OrganizationInput
}