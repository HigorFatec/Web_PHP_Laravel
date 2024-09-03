<!DOCTYPE html>
<html>
<head>
    <title>Reserva de Viagem Finalizado</title>
</head>
<body>
    <p>Olá,</p>
    <p>Informamos que a viagem de {{$reserva->nome}} de <b>{{$reserva->origem}}</b> para <b>{{$reserva->destino}}</b> solicitada foi finalizada.</p> <br>
    <p>Finalizador: <b>{{$user->name}}</b> </p><br>

    <p>Atenciosamente,</p>
    <p><b>Grupo Cargo Polo</b></p>
</body>
</html>
