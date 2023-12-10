import axios from "axios";

const api = axios.create({
    baseURL: `${window.gauthwp_admin.rest_url}gauthwp/v1/`,
    headers: {
        "X-WP-Nonce": window.gauthwp_admin.rest_nonce,
        "Content-Type": "application/json; charset=UTF-8",
        Accept: "application/json; charset=UTF-8",
    },
});

export type ApiActionResult<D> = {
    success: boolean;
    data: D;
};

export default api;
