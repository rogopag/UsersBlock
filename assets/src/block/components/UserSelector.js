import { UsersList } from './UsersList';
import * as api from '../utils/api';
import { uniqueById, debounce } from '../utils/utils';

const { Component } = wp.element;

/**
 * A component to display the users in a select box
 */
export class UserSelector extends Component {
	NUMBER_USERS_PER_PAGE = 10;
	MAX_NUMBER_USERS_FETCH = 100;
	constructor(props) {
		super(...arguments);
		this.props = props;

		this.state = {
			users: [],
			loading: false,
			filter: '',
			filterLoading: false,
			filterUsers: [],
			pages: 1,
			pagesTotal: 0,
			paging: false,
			initialLoading: false,
		};

		this.addUser = this.addUser.bind(this);
		this.removeUser = this.removeUser.bind(this);
		this.handleInputFilterChange = this.handleInputFilterChange.bind(this);
		this.doUserFilter = debounce(this.doUserFilter.bind(this), 300);
		this.doPagination = this.doPagination.bind(this);
	}

	/**
	 * @return {null}
	 */
	componentDidMount() {
		this.setState({ loading: true });

		this.getUsers().then( () => {
			this.setState({ loading: false });
		});

		this.retrieveSelectedUsers()
			.then(() => {
				this.setState({
					initialLoading: false,
				});
				this.getUsers()
					.then(() => {
						this.setState({ loading: false })
					} );
			});
	};

	/**
	 *
	 * @param event
	 */
	handleUserChange(event) {
		this.getUsers().then( () => {
			this.setState({ loading: false} );
		} );
	}



	/**
	 *
	 * @param args
	 * @return {Promise<T | {data: *}>}
	 */
	getUsers( args = {} ) {

		const defaultArgs = {
			per_page: this.NUMBER_USERS_PER_PAGE,
			search: this.state.filter,
			page: this.state.pages
		};

		const requestArguments = {
			...defaultArgs,
			...args
		};

		return api.getUsers( requestArguments )
			.then(response => {
				const { data } = response;
				const users = data.map(user => {
					if ( !user.avatar_urls || user.avatar_urls.length < 1 ) {
						return {
							...user,
							avatar: false
						};
					}

					return {
						...user,
						avatar: user.avatar_urls["24"] || user.avatar_urls["48"] || user.avatar_urls["96"] || false
					}
				});

				return {
					...response,
					data: users
				};
			})
			.then(response => {
				if (requestArguments.search) {
					this.setState({
						filterUsers: requestArguments.page > 1 ? uniqueById([...this.state.filterUsers, ...response.data]) : response.data,
						pages: requestArguments.page,
						pagesTotal: +response.headers['x-wp-totalpages']
					});

					return response;
				}

				this.setState({
					users: uniqueById([...this.state.users, ...response.data]),
					pages: requestArguments.page,
					pagesTotal: +response.headers['x-wp-totalpages']
				});
				// return response to continue the chain
				return response;
			});
	}

	/**
	 *
	 * @return {{readonly id?: *}[]}
	 */
	getSelectedUsers() {
		const { selectedUsers } = this.props;
		return this.state.users
			.filter(({ id }) => selectedUsers.indexOf(id) !== -1)
			.sort((a, b) => {
				const aIndex = this.props.selectedUsers.indexOf(a.id);
				const bIndex = this.props.selectedUsers.indexOf(b.id);

				if (aIndex > bIndex) {
					return 1;
				}

				if (aIndex < bIndex) {
					return -1;
				}

				return 0;
			});
	}

	/**
	 *
	 * @return {null}
	 */
	doUserFilter() {
		const { filter = '' } = this.state;

		if (!filter) {
			return;
		}

		this.setState({
			filtering: true,
			filterLoading: true
		});

		this.getUsers()
			.then(() => {
				this.setState({
					filterLoading: false
				});
			});
	}

	/**
	 *
	 * @return {null}
	 */
	addUser( user_id ) {
		if (this.state.filter) {
			const user = this.state.filterUsers.filter(u => u.id === user_id);
			const users = uniqueById([
				...this.state.users,
				...user
			]);

			this.setState({
				users
			});
		}

		this.props.updateSelectedUsers([
			...this.props.selectedUsers,
			user_id
		]);
	}

	/**
	 * @return null
	 */
	doPagination() {
		this.setState({
			paging: true
		});

		const page = parseInt(this.state.pages, this.NUMBER_USERS_PER_PAGE) + 1 || 2;

		this.getUsers({ page })
			.then(() => this.setState({
				paging: false,
			}));
	}

	/**
	 *
	 * @return {null}
	 */
	removeUser( user_id ) {
		this.props.updateSelectedUsers([
			...this.props.selectedUsers
		].filter(id => id !== user_id));
	}

	/**
	 *
	 * @param iconType
	 * @return {*}
	 */
	getBlockIcon( iconType ) {
		const className = "dashicons dashicons-"+iconType;
		return <span className={className}></span>;
	}

	/**
	 *
	 * @return {*}
	 */
	getSearchIcon() {
		return <span className="dashicons dashicons-search"></span>;
	}

	/**
	 *
	 * @param filter
	 */
	handleInputFilterChange({ target: { value:filter = '' } = {} } = {}) {
		this.setState({
			filter
		}, () => {
			if (!filter) {
				// remove filtered users
				return this.setState({ filteredUsers: [], filtering: false });
			}

			this.doUserFilter();
		})
	}

	/**
	 *
	 * @return {Promise<[*, *, *, *, *, *, *, *, *, *]>|Promise<unknown>}
	 */
	retrieveSelectedUsers() {
		const selectedUsers = this.props.selectedUsers;

		if (!selectedUsers.length > 0) {
			// return a fake promise that auto resolves.
			return new Promise((resolve) => resolve() );
		}

		return new Promise( () => this.getUsers({
				include: selectedUsers.join(','),
				per_page: this.MAX_NUMBER_USERS_FETCH
			})
		);
	}
	/**
	 *
	 * @return {Mixed}
	 */
	render() {
		const canPaginate = (this.state.pages || 1) < this.state.pagesTotal;
		return (
			<div className="user-selector">
				<span className="users-box-title">Users: </span>

				{/*	An input to filter users, it requires additional logic both server and client + caching to be implemented properly
				<div className="filter-users">
					<div className="searchbox">
						<label htmlFor="searchinput">
							<input
								id="searchinput"
								type="search"
								placeholder={"Search users..."}
								defaultValue={this.state.filter}
								onChange={this.handleInputFilterChange}
							/>
							{this.getSearchIcon()}
						</label>
					</div>
				</div> */}

				<div className="user-selectorContainer">
					{/* User List (Selection List) */ }
					<UsersList
						users={ this.state.users }
						loading={ this.state.loading }
						action={this.addUser}
						paging={this.state.paging}
						canPaginate={canPaginate}
						doPagination={this.doPagination}
						icon={this.getBlockIcon('plus')}
					/>
					{/* User List (Selected List) */ }
					<UsersList
						users={ this.getSelectedUsers() }
						loading={ this.state.loading }
						loading={this.state.initialLoading}
						action={this.removeUser}
						icon={this.getBlockIcon('minus')}
					/>
				</div>
			</div>
		);
	}

}

export default UserSelector;
