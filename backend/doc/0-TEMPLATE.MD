# 1.2. `/path/to`

**Description**: DESC

**Path**: `/path/to`  
**Method**: `METHOD`  
**Authentication Required**: ❌✅

---

## 📥 Parameters

| Name       | Type     | Required | Location | Description                                      |
|------------|----------|----------|----------|--------------------------------------------------|
| `email`    | `string` | ✅ Yes   | `body`   | The user's registered email address              |
| `remember` | `bool`   | ❌ No    | `body`   | If true, issues a long-lived session             |

---

## ❗ Errors

| Codice Errore | Nome               | Descrizione                                  |
|---------------|--------------------|----------------------------------------------|
| 1.2.1         | `InvalidCredentials`| Email o password errati                      |
| 1.2.2         | `AccountNotFound`   | Nessun account trovato con questa email      |
| 1.2.3         | `AccountDisabled`   | L’account è stato disabilitato dall’admin    |

---

## ✅ Successful Response

```json
{
  "token": "JWT_TOKEN_STRING",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "role": "admin"
  }
}
```
---
## 🔗 Collegamenti


- Name - [`CODE`](../{Code}-{name}.md)
