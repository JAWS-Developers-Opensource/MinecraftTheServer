import express, { Application, Request, Response, NextFunction } from 'express';
import routes from './routes/index';

const app: Application = express();

// Middleware per parsing JSON
app.use(express.json());

// Middleware che forza la risposta a JSON
app.use((req: Request, res: Response, next: NextFunction) => {
    res.setHeader('Content-Type', 'application/json');
    next();
});

app.use('/v1', routes);
app.use((req: Request, res: Response) => {
    res.status(200).json({ error: 'Endpoint not found' });
});

export default app;
