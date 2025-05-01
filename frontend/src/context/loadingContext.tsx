import { createContext, ReactNode, useContext, useState } from "react";

interface LoadingCtxProps {
    isLoading: boolean;
    setIsLoading: (status: boolean) => void;
}

const LoadingContext = createContext<LoadingCtxProps | undefined>(undefined);

const useLoading = () => {
    const ctx = useContext(LoadingContext);
    if(!ctx)
        throw new Error("CTX not valid [LOADING]");
    
    return ctx;
}

const LoadingProvider = ({children}: {children:ReactNode}) => {
    const [isLoading, setIsLoading] = useState(true);

    return(<LoadingContext.Provider value={{isLoading, setIsLoading}}>{children}</LoadingContext.Provider>);
}

export {useLoading, LoadingProvider, LoadingContext};