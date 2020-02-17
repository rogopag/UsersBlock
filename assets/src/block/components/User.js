export const User = props => {
	const name = props.user.meta.first_name && props.user.meta.last_name ? props.user.meta.first_name + " " + props.user.meta.last_name : props.user.name;
	return( <article className="user">
		<div className="user-body">
			<figure className="user-figure">
				<img src={props.user.avatar} alt={name} />
			</figure>
			<span className="user-title">{name}</span>
		</div>
		<button onClick={() => props.clickHandler(props.user.id)}>{props.icon}</button>
	</article>);
};
