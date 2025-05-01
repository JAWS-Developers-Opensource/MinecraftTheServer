import { APIQuery } from "./api.base";
import { APIResponse, EMPTY_CRED } from "../types/api.types";

export const verifyToken = async (token: string, session: string): Promise<APIResponse> => {
    return await APIQuery({ token: token, session_id: session }, '/user/get/me', 'GET', []);
};

export const login = async (username: string, password: string): Promise<APIResponse> => {
    return await APIQuery(EMPTY_CRED, '/auth/login', 'POST', [], {username: username, password: password});
};

export const logout = async (auth: { token: string, session_id: string }): Promise<APIResponse> => {
    return await APIQuery(auth, '/auth/logout/', 'GET', []);
};