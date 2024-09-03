<!DOCTYPE html>
<html>
<head>
    <title>Reserva de Veiculo Finalizado</title>
</head>
<body>
    <p>Olá,</p>
    <p>Informamos que a Reserva de Veiculo de {{$adiantamento->nome}} de <b>{{$adiantamento->origem}}</b> para <b>{{$adiantamento->destino}}</b> solicitada foi finalizada.</p> <br>
    <p>Finalizador: <b>{{$user->name}}</b> </p><br>

    <p>Atenciosamente,</p>
    <p><b>Grupo Cargo Polo</b></p>
</body>
</html>
