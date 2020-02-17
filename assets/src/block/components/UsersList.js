import { User } from './User';

/**
 *
 * @param props
 * @return {*}
 * @constructor
 */
export const UsersList = props => {
	const { filtered = false, loading = false, users = [], action = () => {}, icon = null} = props;

	if (loading) {
		return <p>Loading users...</p>;
	}

	if (filtered && users.length < 1) {
		return (
			<div className="user-list">
				<p>Your query yielded no results, please try again.</p>
			</div>
		);
	}

	if ( ! users || users.length < 1 ) {
		return <p>No users.</p>
	}

	return (
		<div className="user-list">
			{users.map((user) => <User key={user.id} user={user} clickHandler={action} icon={icon} />)}
			{props.canPaginate ? (<button onClick={props.doPagination} disabled={props.paging}>{props.paging ? 'Loading...' : 'Load More'}</button>) : null}
		</div>
	);
};
