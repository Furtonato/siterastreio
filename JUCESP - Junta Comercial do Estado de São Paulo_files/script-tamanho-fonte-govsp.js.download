// Início do código de Aumentar/ Diminuir a letra
 
// Para usar coloque o comando:
// "javascript:mudaTamanho('tag_ou_id_alvo', -1);" para diminuir
// e o comando "javascript:mudaTamanho('tag_ou_id_alvo', +1);" para aumentar
 
var tagAlvo = new Array('p'); //pega todas as tags p//
 
// Especificando os possíveis tamanhos de fontes, poderia ser: x-small, small...
var tamanhos = new Array('100%', '110%', '120%', '130%', '140%', '150%', '160%');
var tamanhoInicial = 0;

const ativaAumentaTexto = document.getElementById("aumentaFonte");
const ativaReduzTexto = document.getElementById("reduzFonte");

if (ativaAumentaTexto !== false && ativaReduzTexto !== false) {
  ativaAumentaTexto.addEventListener('click', addTamanho);
  ativaReduzTexto.addEventListener('click', removeTamanho);
}

function addTamanho() {
  mudaTamanho('body', +1);
}

function removeTamanho() {
  mudaTamanho('body', -1);
}
 
function mudaTamanho( idAlvo,acao ){
  if (!document.getElementById) return
  var selecionados = null,tamanho = tamanhoInicial,i,j,tagsAlvo;
  tamanho += acao;
  if ( tamanho < 0 ) tamanho = 0;
  if ( tamanho > 6 ) tamanho = 6;
  tamanhoInicial = tamanho;
  if ( !( selecionados = document.getElementById( idAlvo ) ) ) selecionados = document.getElementsByTagName( idAlvo )[ 0 ];
  
  selecionados.style.fontSize = tamanhos[ tamanho ];
  
  for ( i = 0; i < tagAlvo.length; i++ ){
  tagsAlvo = selecionados.getElementsByTagName( tagAlvo[ i ] );
  for ( j = 0; j < tagsAlvo.length; j++ ) tagsAlvo[ j ].style.fontSize = tamanhos[ tamanho ];
  }
}
// Fim do código de Aumentar/ Diminuir tamanho de fontes