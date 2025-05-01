import React, { FormEvent, useEffect, useRef, useState } from "react";
import { useLoading } from "../../context/loadingContext";
import "./LoginScreen.css"
import * as Auht from "../../api/auth";
import autoAnimate from '@formkit/auto-animate';

export const LoginScreen: React.FC = () => {
    const globaLoading = useLoading();

    const [username, setUsername] = useState<string>("");
    const [password, setPassword] = useState<string>("");
    const [loading, setLoading] = useState<boolean>(false);


    const [showForm, setShowForm] = useState<boolean>(false); // Controlla la visibilità degli input

    const formAnimate = useRef(null);
    const loaderAnimate = useRef(null);

    useEffect(() => globaLoading.setIsLoading(false));

    useEffect(() => {
        formAnimate.current && autoAnimate(formAnimate.current, { "duration": 250, "easing": "ease-in-out" });
        loaderAnimate.current && autoAnimate(loaderAnimate.current, { "duration": 500, "easing": "ease-in-out" });
        setShowForm(true);
    }, [formAnimate, loaderAnimate])

    const handleLogin = (_: FormEvent<HTMLFormElement>) => {
        _.preventDefault();

        setShowForm(false);
        setLoading(true);

        Auht.login(username, password).then(res => {
            
        })
    }

    return (
        <>
            <div className="container" ref={formAnimate}>
                {showForm &&
                    <div className="box">
                        <h2 className="heading">Login</h2>
                        <form className={`form`} onSubmit={handleLogin}>
                            <input type="text" placeholder="Username" required className="input" onChange={(event) => setUsername(event.target.value)} />
                            <input type="password" placeholder="Password" required className="input" onChange={(event) => setPassword(event.target.value)} />
                            <button type="submit" className='button' disabled={loading}>Sign In</button>
                        </form>
                    </div>
                }
            </div>
            <div className='loaderWrapper'>
                <div ref={loaderAnimate}>
                    {loading && <div className="loader"></div>}
                </div>
            </div>
        </>
    )
}