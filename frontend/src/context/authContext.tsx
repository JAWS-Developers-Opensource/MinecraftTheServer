import React, { createContext, useContext, useState, ReactNode, useEffect } from 'react';
import { Navigate } from 'react-router-dom';

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within an AuthProvider");
    }
    return context;
};

export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [isAuthenticated, setIsAuthenticated] = useState<boolean>(false);
    const [token, setToken] = useState<string>("");
    const [session_id, setSessionId] = useState<string>("");
    const [association, setAssociation] = useState<Association>({ id: 0, name: "" });
    const [validatingSession, setValidatingSession] = useState(true);
    const [associationRole, setAssociationRole] = useState<string>("");

    const { addNotification } = useNotification();

    const [user, setUser] = useState<User>({
        email: "",
        id: 0,
        name: "",
        profile_picture: "",
        role: [],
        status: false,
        surname: "",
    });
    
    useEffect(() => {
        const savedToken = window.localStorage.getItem('token');
        const savedSession = window.localStorage.getItem('session');
        const savedAssociation = window.localStorage.getItem('association_id');

        // Controllo se ci sono informazioni salvate
        const loginF = async () => {
            try {
                if (savedToken && savedSession && savedAssociation) {
                    // Prova a validare la sessione esistente
                    const response = await loginApi(savedToken, savedSession);
                    if (response.error_code === '2.2.0.3') {
                        const association = await getById({ token: savedToken, session_id: savedSession }, parseInt(savedAssociation));
                        setAssociation({ id: association.data.id, name: association.data.name });
                        setUser(response.data);
                        setAssociationRole(response.data.association_permission[association.data.id]);
                        setIsAuthenticated(true);
                        setToken(savedToken);
                        setSessionId(savedSession);
                        if (!getCookie("gre")) {
                            addNotification("Welcome back", "You are now connected to " + association.data.name, "info");
                            setCookie("gre", "true"); // Il cookie scade dopo 7 giorni
                        }
                    }
                }
            } catch (error) {
                console.error("Error verifying session:", error);
            } finally {
                setValidatingSession(false); // Imposta lo stato `loading` su `false` nel contesto `AuthProvider`
            }
        };

        loginF();
    }, []);

    const login = (newToken: string, newSession: string, association_id: number) => {
        // Memorizza i dettagli della sessione nel localStorage
        window.localStorage.setItem('token', newToken);
        window.localStorage.setItem('session', newSession);
        window.localStorage.setItem('association_id', association_id.toString());

        // Aggiorna lo stato interno
        setToken(newToken);
        setSessionId(newSession);
        setIsAuthenticated(true);

        // Redirigi alla home page o ad una pagina protetta
        window.location.href = "/";
    };

    const changeAssociation = () => {
        window.location.href = `/auth?token=${token}&session=${session_id}`;
    };

    const logout = () => {
        setIsAuthenticated(false);
        Auth.logout({ token, session_id });
        setToken("");
        setSessionId("");
        window.localStorage.removeItem('token');
        window.localStorage.removeItem('session');
        window.localStorage.removeItem('association_id');
        window.localStorage.removeItem('gre');
        //window.location.href = "/auth"; // Redirect alla pagina di login
    };

    if (validatingSession) return <LoadingScreenGlobal />;

    return (
        <AuthContext.Provider value={{ isAuthenticated, login, logout, token, session_id, user, association, changeAssociation, associationRole, validatingSession, auth: { token, session_id } }}>
            {children}
        </AuthContext.Provider>
    );
};

// Updated RequireAuth component
export const RequireAuth = ({ children }: { children: ReactNode }) => {
    const authData = useAuth();

    if (authData.validatingSession) {
        return <LoadingScreenGlobal />;
    }

    if (!authData.isAuthenticated) {
        return <Navigate to="/auth" />;
    }

    return <>{children}</>;
};