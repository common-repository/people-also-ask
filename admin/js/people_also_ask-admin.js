var rodandoCrawler = [];
var requestsPalavras = [];

//Número máximo de itens na fila
var maxItensFila = 1;

jQuery(function ($) {

	
	'use strict';

	const $form = $('#item_form');
	const $helpText = $('#help_text');
	const $list = $('#the-list');
	const $palavra = $('#palavra');
	const $config_max_concurrent_requests = $('#config_max_concurrent_requests');
	const $config_api_scrapper = $('#config_api_scrapper');
	const $config_chave_serp_api = $('#config_chave_serp_api');
	const $config_usuario_data_for_seo = $('#config_usuario_data_for_seo');
	const $config_senha_data_for_seo = $('#config_senha_data_for_seo');	
	const $translate_pendente = $('#translate_pendente');
	const $translate_post_status = $('#translate_post_status');
	const $translate_palavra_chave = $('#translate_palavra_chave');
	const $translate_acoes = $('#translate_acoes');
	const $translate_itens = $('#translate_itens');
	const $translate_selecione_uma_apis = $('#translate_selecione_uma_api');
	const $translate_cadastre_usuario_api_dataforseo = $('#translate_cadastre_usuario_api_dataforseo');
	const $translate_cadastre_senha_api_dataforseo = $('#translate_cadastre_senha_api_dataforseo');
	const $translate_cadastre_chave_api_serpapi = $('#translate_cadastre_chave_api_serpapi');
	const $translate_delete = $('#translate_delete');

	//Requisições simultêneas
	maxItensFila = $config_max_concurrent_requests.val();

	//Processa itens pendentes
	processaPalavrasPendentes();
	
	

	$form.on('submit', (e) => {
	  e.preventDefault();


	  if ($config_api_scrapper.val() == '') {
		alert($translate_selecione_uma_apis.val());
		return;
	  }

	  if ($config_api_scrapper.val() == 'dataforseo') {
		if ($config_usuario_data_for_seo.val() == '') {
			alert($translate_cadastre_usuario_api_dataforseo.val());
			return;
		  }
		  if ($config_senha_data_for_seo.val() == '') {
			alert($translate_cadastre_senha_api_dataforseo.val());
			return;
		  }
	  }

	  if ($config_api_scrapper.val() == 'serpapi') {
		if ($config_chave_serp_api.val() == '') {
			alert($translate_cadastre_chave_api_serpapi.val());
			return;
		  }		  
	  }


	  $helpText.text('');
	  $form[0].checkValidity();
  

	  const data = $form.serializeArray().reduce((curr, { name, value }) => {
		curr[name] = value || null;
		return curr;
	  }, {});
  
	  $.post(people_also_ask_admin_ajax.ajax_url, {
		_ajax_nonce: people_also_ask_admin_ajax.nonce,
		action: 'item_create',
		...data,
	  })
		.done((data) => {

			
			$palavra.trigger('focus');
			$form[0].reset();

			const page = new URLSearchParams(location.search).get('page');
			const pageParam = encodeURIComponent(page);

			//Varre palavra retornada
			data.forEach(item => {

				var row = `
				<tr>
					<th scope="row" class="check-column">
					<input type="checkbox" name="palavra[]" value="${item.id}" />
					</th>
					<td
					class="palavra column-palavra has-row-actions column-primary"
					data-colname="${$translate_palavra_chave.val()}"
					>
					<span class="oms-palavra">${item.palavra}</span>	  
					
					</td>
						<td class="status column-status" data-colname="Status"><span class="col-pendente" data-status="${$translate_pendente.val()}">${$translate_pendente.val()}</span></td>
						<td class="post_status column-post-status" data-colname="${$translate_post_status.val()}">-</td>
						<td class="itens column-itens" data-colname="${$translate_itens.val()}">-</td>
						<td class="acao column-acao" data-colname="${$translate_acoes.val()}">-</td>
					</tr>
					`;

				


				var $noItem = $list.find('.no-items');
				if ($noItem.length) {
					$noItem.replaceWith(row);
				} else {
					$list.prepend(row);
					$('.displaying-num').text((e, val) =>
					val.replace(/\d+/, (d) => parseInt(d, 10) + 1)
					);
				}



			});


			//Processa itens pendentes
			processaPalavrasPendentes();


		})
		.catch((e) => {
		  $helpText.text(e?.responseJSON?.message ?? 'An unknown error');
		});
	});
  });
 


function processaPalavrasPendentes() {

	var palavrasPendentes = retornaPalavrasPendentesProcessamento();

	if (palavrasPendentes.length > 0) {

		for (var i = 0; i < palavrasPendentes.length; i++  ) {

			if (rodandoCrawler.length < maxItensFila) {
				
				setaComoPrcessado(palavrasPendentes[i]);	

			}
			else {

				//console.log('Abandonou: ' + palavrasPendentes[i]);
				break;
			}
		
		}

	}


}


function retornaPalavrasPendentesProcessamento() {


	var palavrasPendentes = [];

	const tds = document.querySelectorAll('table td span[data-status]');
	const $translate_importante = jQuery('#translate_importante');
	const $translate_finalizado = jQuery('#translate_finalizado');
	const $translate_erro = jQuery('#translate_erro');
	const $translate_processando = jQuery('#translate_processando');
	const $translate_sem_conteudo = jQuery('#translate_sem_conteudo');
	
	/*--------- Checa se existem erros graves, de modo a interromper a execução */

	var existemItensGravesComProblema = false;

	tds.forEach(td => {

		if (
			td.getAttribute('data-status').includes($translate_importante.val()) || td.getAttribute('data-status').includes('Importante:')
		) {
			existemItensGravesComProblema = true;
			return;
		}

	});

	

	if (existemItensGravesComProblema == false) {
		
		var itensRetornados = 0;

		for (let i = tds.length - 1; i >= 0; i--) {

			const td = tds[i];
		
			/* ----------------- Se há itens para processar ----------------- */
						
			var input = td.parentNode.parentNode.querySelector('th input');

			//Pega todos os status ('Pendente', 'Perguntas processadas', 'Tópico Pai Processado', 'Pergunta processada 1/4')
			if (
				(
				!td.getAttribute('data-status').includes($translate_finalizado.val())) 
				&& (!td.getAttribute('data-status').includes($translate_erro.val())) 
				&& (!td.getAttribute('data-status').includes($translate_importante.val()))
				&& (!td.getAttribute('data-status').includes($translate_processando.val())) 
				&& (!td.getAttribute('data-status').includes($translate_sem_conteudo.val()))


				&& (!td.getAttribute('data-status').includes('Finalizado')) 
				&& (!td.getAttribute('data-status').includes('Erro')) 
				&& (!td.getAttribute('data-status').includes('Important'))
				&& (!td.getAttribute('data-status').includes('Processando')) 
				&& (!td.getAttribute('data-status').includes('Sem conteúdo'))

				&& (!td.getAttribute('data-status') == "")
			) {

				itensRetornados++;

				if (itensRetornados > maxItensFila)
					break;

				var idPalavra = input.getAttribute('value');

				//Checa se fez menos de 15 requests para essa palavra
				var posPalavra = requestsPalavras.findIndex(arr => arr.includes(idPalavra));

				if (posPalavra >= 0) {

					if (requestsPalavras[posPalavra][1] < 15) {

						//Incrementa total de requests
						requestsPalavras[posPalavra][1]++;

						//Adiciona na lista de palavras pendentes para buscar no servidor
						palavrasPendentes.push(idPalavra);
					}

				}
				else {

					//Adiciona na lista de palavras pendentes para buscar no servidor
					palavrasPendentes.push(idPalavra);

					//Seta que é o primeiro request
					requestsPalavras.push([idPalavra, 1]); 
				}

			}

			/* ----------------- Se há itens para processar ----------------- */
			
		}


	}

	return palavrasPendentes;

}

function setaComoPrcessado(idPalavra) {

	const tds = document.querySelectorAll('table td span[data-status]');
	const $translate_importante = jQuery('#translate_importante');

	var existemItensGravesComProblema = false;

	tds.forEach(td => {

		if (
			td.getAttribute('data-status').includes($translate_importante.val()) || td.getAttribute('data-status').includes('Importante:')
		) {
			console.log('C');
			existemItensGravesComProblema = true;
			return;
		}

	});


	//Seta que está rodando
	var jaRodando = rodandoCrawler.find(element => element == idPalavra);
	if (jaRodando == undefined) {
		rodandoCrawler.push(idPalavra);
	}		
	else {
		console.log('Item já existente: ' + idPalavra);
		return;
	}
		

	const $translate_erro = jQuery('#translate_erro');
	const $translate_sem_conteudo = jQuery('#translate_sem_conteudo');
	const $translate_finalizado = jQuery('#translate_finalizado');
	const $translate_editar_post = jQuery('#translate_editar_post');
	const $translate_visualizar = jQuery('#translate_visualizar');
	const $translate_processando = jQuery('#translate_processando');
	const $translate_rascunho = jQuery('#translate_rascunho');
	const $translate_publicado = jQuery('#translate_publicado');
	const $translate_pendente = jQuery('#translate_pendente');

	//console.group('setaComoPrcessado: ', idPalavra, 'itensNaFila: ', rodandoCrawler.length, 'maxItensFila: ', maxItensFila, 'rodandoCrawler: ', rodandoCrawler);
	//console.groupEnd();

	var checkbox = document.querySelector('input[name="palavra[]"][value="' + idPalavra +'"]');

	if (checkbox != null)  {
		
		
		var colStatus = checkbox.parentNode.parentNode.querySelector('.status');
		var colPostStatus = checkbox.parentNode.parentNode.querySelector('.post_status');
		var colItens = checkbox.parentNode.parentNode.querySelector('.itens');
		var colAcao = checkbox.parentNode.parentNode.querySelector('.acao');
		var config_status_posts = document.querySelector('#config_status_posts');

		if (colStatus.querySelector('span').getAttribute('data-status').indexOf($translate_pendente.val()) >= 0) {
			colStatus.innerHTML = '<span class="col-processando" data-status="' + $translate_processando.val() +'">' + $translate_processando.val() +'...</span>';
		}
			
		//console.group(idPalavra, checkbox.parentNode.parentNode, colPostStatus);

		jQuery.post(people_also_ask_admin_ajax.ajax_url, {
			_ajax_nonce: people_also_ask_admin_ajax.nonce,
			action: 'processa_item',
			'idPalavra': idPalavra
		  }).done(function(data) {

			

			//Seta que finalizou o Crawler
			var buscaItemArray = rodandoCrawler.indexOf(idPalavra);  // Busca o índice do item
			if (buscaItemArray !== -1) {
				rodandoCrawler.splice(buscaItemArray, 1);
			}
			

			//Seta status de Sucesso
			if (data.status == $translate_finalizado.val() || data.status == 'Finalizado') {
			
				colStatus.innerHTML = '<span class="col-finalizado" data-status="' + data.status +'">' + data.status +'</span>';

				//Total de Itens
				if (data.total_itens > 0)
					colItens.innerHTML = data.total_itens;
				else
					colItens.innerHTML = '-';

				//Status do Post
				if (config_status_posts.value == 'automatico-publicado') {
					colPostStatus.innerHTML = '<span class="col-finalizado">'+$translate_publicado.val()+'</span>';
				}
				else {
					colPostStatus.innerHTML = '<span class="col-rascunho">'+$translate_rascunho.val()+'</span>';
				}

				//Botões de Ação
				if (data.wp_post_id > 0)
					colAcao.innerHTML = '<a href="../?p='+data.wp_post_id+'&preview=true" target="_blank" class="btn button-primary" target="_blank">' + $translate_visualizar.val() +'</a> <a href="post.php?post='+data.wp_post_id+'&action=edit" target="_blank" class="btn button-primary" target="_blank">' + $translate_editar_post.val() + '</a>';
				else
					colAcao.innerHTML = '';

			}				
			else {
				
				if (data.status == $translate_sem_conteudo.val() || data.status == $translate_erro.val() || data.status == 'Sem conteúdo' || data.status == 'Erro')
					colStatus.innerHTML = '<span class="col-pendente" data-status="' + data.status +'">' + data.status +'...</span>';
				else
					colStatus.innerHTML = '<span class="col-processando" data-status="' + data.status +'">' + data.status +'...</span>';

			}
				
			//Checa se ainda há itens pendentes
			setTimeout(function() {
				processaPalavrasPendentes();
			}, 500)

		  }).catch((e) => {

			//Seta que finalizou o Crawler
			var buscaItemArray = rodandoCrawler.indexOf(idPalavra);  // Busca o índice do item
			if (buscaItemArray !== -1) {
				rodandoCrawler.splice(buscaItemArray, 1);
			}
				
			//Seta status de Errro
			colStatus.innerHTML = '<span class="col-pendente" data-status="' + (e?.responseJSON?.message ?? $translate_erro.val()) +'">' + (e?.responseJSON?.message ?? $translate_erro.val()) +'</span>';

			//Checa se ainda há itens pendentes
			setTimeout(function() {
				processaPalavrasPendentes();
			}, 500)

		  });


		
	}
		

}



function exibeCamposCredenciais(elm) {
        
	if (elm.value == 'dataforseo') {
		[].forEach.call(document.querySelectorAll('.tr-dataforseo'), function(div) {
			div.style.display = 'table-row';
		});
		[].forEach.call(document.querySelectorAll('.tr-serpapi'), function(div) {
			div.style.display = 'none';
		});
	}
	else if (elm.value == 'serpapi') {
		[].forEach.call(document.querySelectorAll('.tr-serpapi'), function(div) {
			div.style.display = 'table-row';
		});
		[].forEach.call(document.querySelectorAll('.tr-dataforseo'), function(div) {
			div.style.display = 'none';
		});
	}
	else {
		[].forEach.call(document.querySelectorAll('.tr-serpapi'), function(div) {
			div.style.display = 'none';
		});
		[].forEach.call(document.querySelectorAll('.tr-dataforseo'), function(div) {
			div.style.display = 'none';
		});
	}

}

document.addEventListener('DOMContentLoaded', function() {
    var apiScrapper = document.getElementById('api-scrapper');
    
    if (apiScrapper != null) {
        exibeCamposCredenciais(apiScrapper);
    }
});