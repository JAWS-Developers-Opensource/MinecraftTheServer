import express from 'express'
import cors from 'cors'

const app = express()
app.use(cors())
app.use(express.json())

app.get('/api/hello', (_req, res) => {
    res.json({ message: 'Backend is running!' })
})

app.listen(3001, () => console.log('API on http://localhost:3001'))