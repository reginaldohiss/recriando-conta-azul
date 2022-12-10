<?php
class Nfe extends model {

	public function emitirNFE($cNF, $destinatario, $prods, $fatinfo) {
		$nfe = new NFePHP\NFe\MakeNFe();
		$nfeTools = new NFePHP\NFe\ToolsNFe("nfe/files/config.json");

		//Dados da NFe - infNFe
		$cUF = $nfeTools->aConfig['cUF']; //codigo numerico do estado
		$natOp = 'Venda de Produto'; //natureza da operação
		$indPag = '0'; //0=Pagamento à vista; 1=Pagamento a prazo; 2=Outros
		$mod = '55'; //modelo da NFe 55 ou 65 essa última NFCe
		$serie = '1'; //serie da NFe
		$nNF = $cNF; // numero da NFe
		$dhEmi = date("Y-m-d\TH:i:sP"); // Data de emissão
		$dhSaiEnt = date("Y-m-d\TH:i:sP"); //Data de entrada/saida
		$tpNF = '1'; // 0=entrada; 1=saida
		$idDest = '1'; //1=Operação interna; 2=Operação interestadual; 3=Operação com exterior.
		$cMunFG = $nfeTools->aConfig['cMun']; // Código do Município
		$tpImp = '1'; //0=Sem geração de DANFE; 1=DANFE normal, Retrato; 2=DANFE normal, Paisagem; 3=DANFE Simplificado; 4=DANFE NFC-e; 5=DANFE NFC-e em mensagem eletrônica
		$tpEmis = '1'; //1=Emissão normal (não em contingência);
		               //2=Contingência FS-IA, com impressão do DANFE em formulário de segurança;
		               //3=Contingência SCAN (Sistema de Contingência do Ambiente Nacional);
		               //4=Contingência DPEC (Declaração Prévia da Emissão em Contingência);
		               //5=Contingência FS-DA, com impressão do DANFE em formulário de segurança;
		               //6=Contingência SVC-AN (SEFAZ Virtual de Contingência do AN);
		               //7=Contingência SVC-RS (SEFAZ Virtual de Contingência do RS);
		               //9=Contingência off-line da NFC-e (as demais opções de contingência são válidas também para a NFC-e);
		               //Nota: Para a NFC-e somente estão disponíveis e são válidas as opções de contingência 5 e 9.
		$tpAmb = $nfeTools->aConfig['tpAmb']; //1=Produção; 2=Homologação
		$finNFe = '1'; //1=NF-e normal; 2=NF-e complementar; 3=NF-e de ajuste; 4=Devolução/Retorno.
		$indFinal = '1'; //0=Normal; 1=Consumidor final;
		$indPres = '2'; //0=Não se aplica (por exemplo, Nota Fiscal complementar ou de ajuste);
		               //1=Operação presencial;
		               //2=Operação não presencial, pela Internet;
		               //3=Operação não presencial, Teleatendimento;
		               //4=NFC-e em operação com entrega a domicílio;
		               //9=Operação não presencial, outros.
		$procEmi = '0'; //0=Emissão de NF-e com aplicativo do contribuinte;
		                //1=Emissão de NF-e avulsa pelo Fisco;
		                //2=Emissão de NF-e avulsa, pelo contribuinte com seu certificado digital, através do site do Fisco;
		                //3=Emissão NF-e pelo contribuinte com aplicativo fornecido pelo Fisco.
		$verProc = $nfeTools->aConfig['vApp']; //versão do aplicativo emissor
		$dhCont = ''; //entrada em contingência AAAA-MM-DDThh:mm:ssTZD
		$xJust = ''; //Justificativa da entrada em contingência
		$cnpj = $nfeTools->aConfig['cnpj']; // CNPJ do emitente

		//Numero e versão da NFe (infNFe)
		$ano = date('y', strtotime($dhEmi));
		$mes = date('m', strtotime($dhEmi));
		
		$chave = $nfe->montaChave($cUF, $ano, $mes, $cnpj, $mod, $serie, $nNF, $tpEmis, $nNF);
		$versao = $nfeTools->aConfig['nfeVersao'];
		$resp = $nfe->taginfNFe($chave, $versao);

		$cDV = substr($chave, -1); //Digito Verificador da Chave de Acesso da NF-e, o DV é calculado com a aplicação do algoritmo módulo 11 (base 2,9) da Chave de Acesso.
		//tag IDE
		$resp = $nfe->tagide($cUF, $nNF, $natOp, $indPag, $mod, $serie, $nNF, $dhEmi, $dhSaiEnt, $tpNF, $idDest, $cMunFG, $tpImp, $tpEmis, $cDV, $tpAmb, $finNFe, $indFinal, $indPres, $procEmi, $verProc, $dhCont, $xJust);

		//Dados do emitente
		$CPF = ''; // Para Emitente CPF
		$xNome = $nfeTools->aConfig['razaosocial'];
		$xFant = $nfeTools->aConfig['nomefantasia'];
		$IE = $nfeTools->aConfig['ie']; // Inscrição Estadual
		$IEST = $nfeTools->aConfig['iest']; // IE do Substituti Tributário
		$IM = $nfeTools->aConfig['im']; // Inscrição Municipal
		$CNAE = $nfeTools->aConfig['cnae']; // CNAE Fiscal
		$CRT = $nfeTools->aConfig['regime']; // CRT (Código de Regime Tributário), 1=simples nacional
		$resp = $nfe->tagemit($cnpj, $CPF, $xNome, $xFant, $IE, $IEST, $IM, $CNAE, $CRT);

		//endereço do emitente
		$xLgr = $nfeTools->aConfig['xLgr'];
		$nro = $nfeTools->aConfig['nro'];
		$xCpl = $nfeTools->aConfig['xCpl'];
		$xBairro = $nfeTools->aConfig['xBairro'];
		$cMun = $nfeTools->aConfig['cMun'];
		$xMun = $nfeTools->aConfig['xMun'];
		$UF = $nfeTools->aConfig['UF'];
		$CEP = $nfeTools->aConfig['CEP'];
		$cPais = $nfeTools->aConfig['cPais'];
		$xPais = $nfeTools->aConfig['xPais'];
		$fone = $nfeTools->aConfig['fone'];
		$resp = $nfe->tagenderEmit($xLgr, $nro, $xCpl, $xBairro, $cMun, $xMun, $UF, $CEP, $cPais, $xPais, $fone);

		//destinatário
		$CNPJ = $destinatario['cnpj'];
		$CPF = $destinatario['cpf'];
		$idEstrangeiro = $destinatario['idestrangeiro'];
		$xNome = $destinatario['nome']; // Nome/Razão Social
		$email = $destinatario['email'];
		$indIEDest = $destinatario['iedest']; // Indica se tem IE (vazio ou 1)
		$IE = $destinatario['ie']; // Insc. Estadual
		$ISUF = $destinatario['isuf']; // Insc. SUFRAMA
		$IM = $destinatario['im']; // Insc. Municipal
		$resp = $nfe->tagdest($CNPJ, $CPF, $idEstrangeiro, $xNome, $indIEDest, $IE, $ISUF, $IM, $email);

		//Endereço do destinatário
		$xLgr = $destinatario['end']['logradouro'];
		$nro = $destinatario['end']['numero'];
		$xCpl = $destinatario['end']['complemento'];
		$xBairro = $destinatario['end']['bairro'];
		$xMun = $destinatario['end']['mu'];
		$UF = $destinatario['end']['uf'];
		$CEP = $destinatario['end']['cep'];
		$xPais = $destinatario['end']['pais'];
		$fone = $destinatario['end']['fone'];
		$cMun = $destinatario['end']['cmu']; // Código do Municipio
		$cPais = $destinatario['end']['cpais']; // Código do País
		$resp = $nfe->tagenderDest($xLgr, $nro, $xCpl, $xBairro, $cMun, $xMun, $UF, $CEP, $cPais, $xPais, $fone);

		$vBCST = 0;
		$vST = 0;
		$vII = 0;
		$vIPI = 0;
		$vTotFrete = 0;
		$vTotSeg = 0;
		$vTotDesc = 0;
		$vTotOutro = 0;
		$vTotTrib = 0;
		$vTotal = 0;

		foreach($prods as $pchave => $prod) {
			$nItem = ($pchave+1);

		    $cProd = $prod['cProd'];
		    $cEAN = $prod['cEAN'];
		    $xProd = $prod['xProd'];
		    $NCM = $prod['NCM'];
		    $EXTIPI = $prod['EXTIPI'];
		    $CFOP = $prod['CFOP'];
		    $uCom = $prod['uCom'];
		    $qCom = $prod['qCom'];
		    $vUnCom = $prod['vUnCom'];
		    $vProd = $prod['vProd'];
		    $vBC = $prod['vBC'];
		    $cEANTrib = $prod['cEANTrib'];
		    $uTrib = $prod['uTrib'];
		    $qTrib = $prod['qTrib'];
		    $vUnTrib = $prod['vUnTrib'];
		    $vFrete = $prod['vFrete'];
		    $vSeg = $prod['vSeg'];
		    $vDesc = $prod['vDesc'];
		    $vOutro = $prod['vOutro'];
		    $indTot = $prod['indTot'];
		    $xPed = $prod['xPed'];
		    $nItemPed = $prod['nItemPed'];
		    $nFCI = $prod['nFCI'];
		    $cst = $prod['cst'];
		    $pPIS = $prod['pPIS'];
	        $pCOFINS = $prod['pCOFINS'];
	        $csosn = $prod['csosn'];
	        $pICMS = $prod['pICMS'];
		    $orig = $prod['orig'];
	        $modBC = $prod['modBC'];
	        $vICMSDeson = $prod['vICMSDeson'];
	        $pRedBC = $prod['pRedBC'];
	        $modBCST = $prod['modBCST'];
	        $pMVAST = $prod['pMVAST'];
	        $pRedBCST = $prod['pRedBCST'];
	        $vBCSTRet = $prod['vBCSTRet'];
	        $vICMSSTRet = $prod['vICMSSTRet'];
	        $qBCProd = $prod['qBCProd'];
	        $vAliqProd = $prod['vAliqProd'];

		    $vICMS = number_format(($pICMS/100)*$vProd, 2);//$vICMS = '9.00';
		    $vPIS = number_format(($pPIS/100)*$vProd, 2);//'0.32';
	        $vCOFINS = number_format(($pCOFINS/100)*$vProd, 2);//'1.50';
		    $vBCST = $vBC;
	        $pICMSST = $pICMS;
	        $vICMSST = $vICMS;
		    
		    $pCredSN = $pICMS;
		    $vCredICMSSN = $vICMS;

		    // adicionar produto
		    $nfe->tagprod($nItem, $cProd, $cEAN, $xProd, $NCM, $EXTIPI, $CFOP, $uCom, $qCom, $vUnCom, $vProd, $cEANTrib, $uTrib, $qTrib, $vUnTrib, $vFrete, $vSeg, $vDesc, $vOutro, $indTot, $xPed, $nItemPed, $nFCI);
		    
		    // adicionar ICMS Simples Nacional
		    $nfe->tagICMSSN($nItem, $orig, $csosn, $modBC, $vBC, $pRedBC, $pICMS, $vICMS, $pCredSN, $vCredICMSSN, $modBCST, $pMVAST, $pRedBCST, $vBCST, $pICMSST, $vICMSST, $vBCSTRet, $vICMSSTRet);

		    // adicionar PIS
		    $nfe->tagPIS(
		        $nItem,
		        $cst,
		        $vBC,
		        $pPIS,
		        $vPIS,
		        $qBCProd,
		        $vAliqProd
		    );

		    // adicionar COFINS
		    $nfe->tagCOFINS(
		        $nItem,
		        $cst,
		        $vBC,
		        $pCOFINS, // Alíquota do COFINS (em %)
		        $vCOFINS,
		        $qBCProd, // Quantidade vendida
		        $vAliqProd// Alíquota do PIS (em reais)
		    );

		    // Imposto Total deste produto
		    $vTrib = $vICMS+$vPIS+$vCOFINS;

		    $nfe->tagimposto($nItem, number_format($vTrib, 2));

			$vBCST += $vICMS;
			$vST += $vICMS;
			$vTotFrete += $vFrete;
			$vTotSeg += $vSeg;
			$vTotDesc += $vDesc;
			$vTotOutro += $vOutro;
			$vTotTrib += $vTrib;
			$vTotal += ($vProd+$vICMS+$vFrete+$vSeg-$vDesc+$vOutro+$vTrib);
		}

		$vTotTrib = number_format($vTotTrib, 2);
		// adicionar grupo de ICMS total
		$nfe->tagICMSTot($vBCST, $vICMS, $vICMSDeson, $vBCST, $vST, $vProd, $vTotFrete, $vTotSeg, $vTotDesc, $vII, $vIPI, $vPIS, $vCOFINS, $vTotOutro, $vTotal, $vTotTrib);
		
		// Frete
		$nfe->tagtransp($fatinfo['modFrete']); //0=Por conta do emitente; 1=Por conta do destinatário/remetente; 2=Por conta de terceiros; 9=Sem Frete;
		
		// Dados da fatura
		$nFat = $fatinfo['nfat']; // Número da Fatura
		$vOrig = $fatinfo['vorig']; // Valor original da fatura
		$vDesc = $fatinfo['vdesc']; // Valor do desconto
		$vLiq = ($fatinfo['vorig'] - $fatinfo['vdesc']); // Valor Líquido
		$nfe->tagfat($nFat, $vOrig, $vDesc, $vLiq);

		// Monta a NF-e e retorna o resultado
		$resp = $nfe->montaNFe();
		if($resp === true) {
			$xml = $nfe->getXML();

			// Assina o XML
			$xml = $nfeTools->assina($xml);

			// Valida o XML
			$v = $nfeTools->validarXml($xml);
			
			if($v == false) {
				foreach($nfeTools->errors as $erro) {
					if(is_array($erro)) {
						foreach($erro as $er) {
							echo $er."<br/>";
						}
					} else {
						echo $erro."<br/>";
					}
				}

				exit;
			}

			$idLote = '';
			$indSinc = '0'; // 0=asíncrono, 1=síncrono
			$flagZip = false;
			$resposta = array();

			// Envia para o SEFAZ
			$nfeTools->sefazEnviaLote($xml, $tpAmb, $idLote, $resposta, $indSinc, $flagZip);

			// Consulta o RECIBO
			$protXML = $nfeTools->sefazConsultaRecibo($resposta['nRec'], $tpAmb);

			// Chave aleatória para o XML/PDF
			$chave = md5(time().rand(0,9999));
			$xmlName = $chave.'.xml';
			$danfeName = $chave.'.pdf';

			// Salva os arquivos temporário e validado
			$pathNFefile = "nfe/files/nfe/validadas/".$xmlName;
			$pathProtfile = "nfe/files/nfe/temp/".$xmlName;
			$pathDanfeFile = "nfe/files/nfe/danfe/".$danfeName;
			file_put_contents($pathNFefile, $xml);
			file_put_contents($pathProtfile, $protXML);

			// Adiciona o Protocolo
			$nfeTools->addProtocolo($pathNFefile, $pathProtfile, true);

			// Gera o DANFE
			$docxml = NFePHP\Common\Files\FilesFolders::readFile($pathNFefile);

			$docFormat = $nfeTools->aConfig['aDocFormat']->format;
			$docPaper = $nfeTools->aConfig['aDocFormat']->paper;
			$docLogo = $nfeTools->aConfig['aDocFormat']->pathLogoFile;

			$danfe = new NFePHP\Extras\Danfe($docxml, $docFormat, $docPaper, $docLogo);
			$danfe->montaDANFE();
			$danfe->printDANFE($pathDanfeFile, "F");

			return $chave;
		} else {
			foreach($nfe->erros as $erro) {
				echo $erro['tag'].' - '.$erro['desc']."<br/>";
			}
		}

	}

}