import axios from 'axios';

/**
 * Makes a get request to the Users endpoint.
 *
 * @returns {AxiosPromise<any>}
 */
export const getUsers = ( {...args } ) => {
	const queryString = Object.keys(args).map(arg => `${arg}=${args[arg]}`).join('&');
	return axios.get(`/wp-json/wp/v2/users?${queryString}&_embed`);
};
