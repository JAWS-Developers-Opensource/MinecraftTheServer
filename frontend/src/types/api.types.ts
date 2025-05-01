export type APIResponse = {
    request_id: string,
    error_code: string,
    speed: string,
    data?: any
}

export const EMPTY_CRED = { token: "", session_id: ""}