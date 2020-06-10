
# Отбор на стажировку ВК 2020

## API:

### createGame.php - создает новую игру

Возвращает **"status"** - **"OK"**, если игра успешно создана или **"Fail",** если произошла ошибка.

&nbsp;&nbsp;&nbsp;&nbsp;Если **"status"** = **"OK"**, то возвращает **"id"** созданной игры.

&nbsp;&nbsp;&nbsp;&nbsp;Если **"status"** = **"Fail"**, то возвращает **"message"** с сообщением об ошибке.

---

### play.php - принимает "id" текущей игры и "method" - описание текущего действия.

 Если **"method"**=**"print"**:

Возвращает **"status"** - **"OK",** если запрос успешно выполнен или **"Fail"**, если произошла ошибка.

 - Если **"status"** = **"Fail"**, то возвращает **"message"** с сообщением об ошибке.

 - Если **"status"** = **"OK"**, то возвращает **"board"** - строку из 64 символов с текущим описанием доски (символы задают доску слева направо и потом сверху вниз), **"turn"** = **"Black"** или **"White"** - очередность текущего хода и **"gameState"** = **"Normal"** или **"Check"** или **"Mate"** - обычное состояние, шах или мат сейчас поставлен текущему игроку.
---  
  
Если **"method"**=**"makeTurn"**, то принимает также **"start"** и **"end"** - шахматные координаты (например E4), описание текущего хода (фигура на позиции **"start"** переходит на позицию **"end"**):
 
  Возвращает **"status"** - **"OK"**, если запрос успешно выполнен или **"Fail"**, если произошла ошибка.
  - Если **"status"** = **"Fail",** то возвращает "message" с сообщением об ошибке.
 
  - Если **"status"** = **"OK"**, значит текущий ход выполнен.

---
