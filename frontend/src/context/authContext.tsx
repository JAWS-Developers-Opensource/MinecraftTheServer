import React, { createContext, useContext, useState, ReactNode, useEffect } from 'react';
import { Navigate } from 'react-router-dom';

const AuthContext = createContext<any | undefined>(undefined);

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within an AuthProvider");
    }
    return context;
};

export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [isAuthenticated, setIsAuthenticated] = useState<boolean>(false);


    const [user, setUser] = useState<any>({
        username: "",
    });
    
    useEffect(() => {
        const savedToken = window.localStorage.getItem('token');
        const savedSession = window.localStorage.getItem('session');
        const savedAssociation = window.localStorage.getItem('association_id');

        // Controllo se ci sono informazioni salvate
        const loginF = async () => {
            
        };

        loginF();
    }, []);

    const login = (newToken: string, newSession: string, association_id: number) => {
       
    };

    const logout = () => {
       
    };


    return (
        <AuthContext.Provider value={{ isAuthenticated, login, logout, user}}>
            {children}
        </AuthContext.Provider>
    );
};

// Updated RequireAuth component
export const RequireAuth = ({ children }: { children: ReactNode }) => {
    const authData = useAuth();

    if (authData.validatingSession) {
        
    }

    if (!authData.isAuthenticated) {
        return <Navigate to="/auth" />;
    }

    return <>{children}</>;
};