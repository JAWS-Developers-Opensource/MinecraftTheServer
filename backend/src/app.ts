// Codice di ChatGPT
import express, { Application, Request, Response, NextFunction } from 'express';
import cors from 'cors';
import routes from './routes';
import { error } from 'console';

const app: Application = express();

// Middleware per parsing JSON
app.use(express.json());

// Middleware che forza la risposta a JSON
app.use((req: Request, res: Response, next: NextFunction) => {
    res.setHeader('Content-Type', 'application/json');
    next();
});

app.use('/v1', routes);
app.use('/', async (req: Request, res: Response): Promise<void> => {
    res.json({
        status: "error",
        error: "Unkonw version"
    });
})

export default app;
