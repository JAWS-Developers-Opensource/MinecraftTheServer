// Codice di ChatGPT
import { Request, Response } from 'express';

export const login = async (req: Request, res: Response): Promise<void> => {
    console.log(req.body);
    
    res.json({});
};