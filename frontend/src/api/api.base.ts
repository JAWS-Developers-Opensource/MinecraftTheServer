import { APIResponse } from "../types/api.types.ts";
export var lastExecutiondata: APIResponse;
const API_URL = "http://127.20.0.11:3000";
export const APIQuery = async (auth: { token: string, session_id: string }, path: string, method: 'POST' | 'GET', headers_values: { name: string, value: string }[], postData?: any): Promise<APIResponse> => {
    try {
        const response = await fetch(`${API_URL}${path}`, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${auth.token}`,
                'x-session-id': auth.session_id,
                ...headers_values.reduce((acc, { name, value }) => {
                    acc[name] = value;
                    return acc;
                }, {} as Record<string, string>)
            },
            body: JSON.stringify(postData)
        });
        const data = await (response.json());
        lastExecutiondata = data;

        if (data.error_code === "1.4.2")
            window.location.reload();

        return data;
    } catch (err) {
        lastExecutiondata = { error_code: "0", request_id: "", speed: "0", data: err }
        return { error_code: "0", request_id: "", speed: "0", data: err }
    }
}