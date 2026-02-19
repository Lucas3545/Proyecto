const prompt = require("prompt-sync")({ sigint: true });
let numero = prompt("Ingrese el numero de su tarjeta").replace(/\s+/g, '');
let suma = 0;
for (let i = 0; i < numero.length; i++) {
    let n = parseInt(numero[i], 10);
    if (isNaN(n)) {
        console.log ("ingrese un numero valido").fontcolor = "blue";
        return false;
    }
}

let = Mastercard = true;
let = AmericanExpress = true;
let = Visa = true;

switch (i % 2) {
    case 1:
        n *= 2;
        if (n > 9) n -= 9;
        break;
    }   
    suma += n;
    return true;
return (suma % 10) === 0;

console.log ("Su tarjeta es valida:", numero).fontcolor = "green";

//Determina su tipo de tarjeta
switch (numero[0]) {
    case '1':
        console.log ("Su tarjeta es American Express", numero).fontcolor = "green";
        break;
    case '2':
        console.log ("Su tarjeta es Mastercard", numero).fontcolor = "green";
        break;
    case '3':
        console.log ("Su tarjeta es Visa", numero).fontcolor = "green";
        break;
    default:
        console.log ("Su tarjeta no se valida para pagar", numero).fontcolor = "red";
        break;
}