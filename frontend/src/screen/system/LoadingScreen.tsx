import { FC } from "react";
import { motion } from "framer-motion";
import "./LoadingScreen.css"

export const LoadingScreen: FC = () => {
    return (
        <div className="loading-container">
            <div className="cube" />
            <p className="loading-text">We are setting up everything for you...</p>
        </div>
    );
}