import java.io.BufferedInputStream;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;
import java.security.KeyManagementException;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;
import java.security.cert.X509Certificate;
import java.util.zip.GZIPInputStream;
import java.util.zip.GZIPOutputStream;

import javax.net.ssl.HttpsURLConnection;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;
import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSession;

/* Autoria: TCE/SC - www.tce.sc.gov.br
 * Data atualização: 27/6/2016 
 *
 * Descrição:
 * 
 *  Exemplo de uso do WebService do TCE/SC para envio de dadas ao sistema e-Sfinge.
 *  O objetivo deste exemplo é entender melhor o processo de envio dos dados. Por ser
 *  somente um exemplo para fins didáticos que não usa nenhum recurso de geração 
 *  automática de código ou facilitadores na implementação.
 *  
 *      No caso real, cada usuário/empresa pode escolher a linguagem/framework que
 *  melhor lhe atende, sem qualquer prejuízo no envio.
 *  */

public class Exemplo_eSfingeWS2016 {
	
	private static String SERVER_BASE = "https://desenv2.tce.sc.gov.br:7443";  // testes/homologação
	//private static String SERVER_BASE = "https://esfingews.tce.sc.gov.br"; // produção
	
	private static String LANC_CONTAB_PU = SERVER_BASE + "/esfinge/services/lancamentoContabilPUWS?wsdl";
	private static String URL_SERVICO_COMPETENCIA = SERVER_BASE + "/esfinge/services/competenciaWS?wsdl";
	private static String URL_SERVICO_CONSULTA_ANTIGO = SERVER_BASE + "/esfinge/services/consultaWS?wsdl";
	private static String URL_SERVICO_ARQ_FISICO = SERVER_BASE + "/esfinge/services/arquivoFisicoWS?wsdl";
		
	private static String URL_SERVICO_MENSAGEM = SERVER_BASE + "/esfinge/services/mensagemWS?wsdl";
	private static String XMLNS_PREFIXO_SERVICO = "lanccontpu";
	private static String TOKEN = SERVER_BASE + "/esfinge/services/tokenWS?wsdl";
	private static String INICIO_TOKEN="xsi:type=\"xs:string\">";
	
	private static int SERVICO_ENVIAR_ROLLBACK = 1;
	private static int SERVICO_ENVIAR_COMMIT = 2;
	private static int SERVICO_CONSULTA = 10;
	private static int SERVICO_CONSULTA_ANTIGO_LISTAR = 11;
	private static int SERVICO_CONSULTA_ANTIGO_LISTARCamposPorTabela = 12;
	private static int SERVICO_CONSULTA_ANTIGO_LISTAROperadoresFiltroConsultaDisponiveis = 13;
	private static int SERVICO_CONSULTA_ANTIGO_LISTARTabelasDisponiveis = 14;
	private static int SERVICO_CONSULTA_ANTIGO_LISTARRelatoriosDisponiveis = 15;
	private static int SERVICO_CONSULTA_ANTIGO_LISTARRelatorio = 16;
	private static int SERVICO_ARQUIVO_FISICO = 20;
	private static int SERVICO_ARQUIVO_LISTAR = 21;
	private static int SERVICO_ARQUIVO_DOWNLOAD = 22;
	private static int SERVICO_MENSAGEM = 30;
	private static int SERVICO_SERVICO_COMPETENCIA = 40;
	private static int ID_TABELA = 20;
	private static int ID_BALANCETE = 1;

	private static HttpURLConnection urlConn = null;
	
	// Configuração
	private static boolean usarGZIP = true; // obrigatório desde 11/09/2015 
/*LOG*/	private static int numPacote = 0;
/*LOG*/	private static boolean showGZIPLog = false;
/*LOG*/	private static boolean showWS_HeaderLog = true;
/*LOG*/	private static boolean showHeaderLog = false;
/*LOG*/	private static boolean showXMLPlanLog = true;

	public static void main(String[] args) throws KeyManagementException, MalformedURLException, ProtocolException, NoSuchAlgorithmException, IOException {

		String user = "";  // USUARIO_PERFIL_ESFINGEWS
    	String pass = "";  // SENHA;
    	String competencia = ""; // COMPETENCIA
    	String  codUG = ""; // CODIGO_UG

    	testeAll(user,pass,competencia,codUG);
   	}


	public static void testeAll(String user, String pass,String competencia,String codUG){
      	executaTeste(SERVICO_MENSAGEM,user,pass,competencia,codUG,"OK");
      	executaTeste(SERVICO_SERVICO_COMPETENCIA,user,pass,competencia,codUG,"OK");
        executaTeste(SERVICO_CONSULTA_ANTIGO_LISTAR,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_CONSULTA_ANTIGO_LISTARTabelasDisponiveis,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_CONSULTA_ANTIGO_LISTAROperadoresFiltroConsultaDisponiveis,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_CONSULTA_ANTIGO_LISTARCamposPorTabela,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_CONSULTA_ANTIGO_LISTARRelatoriosDisponiveis,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_CONSULTA_ANTIGO_LISTARRelatorio,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_CONSULTA,user,pass,competencia,codUG,"OK");
    	
    	executaTeste(SERVICO_ARQUIVO_FISICO,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_ARQUIVO_LISTAR,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_ARQUIVO_DOWNLOAD,user,pass,competencia,codUG,"OK");
    	executaTeste(SERVICO_ENVIAR_ROLLBACK,user,pass,competencia,codUG,"OK");

	}

	
	public static void executaTeste(int tipoTexte,String user, String pass,String competencia,String codUG,String coment){
		/*LOG*/	System.out.println("######################### TipoTexte: " + tipoTexte + " USR:"+ user + "/" + pass + " - " + competencia + " codUG=" + codUG + " - " + coment + " #########################");
		numPacote = 0;
		String token = null;
        String ret = null; 

		String header = "<?xml version='1.0' encoding='UTF-8'?><soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\"><soap:Header><wsse:Security xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\" soap:mustUnderstand=\"1\">"
      		     + "<wsse:UsernameToken xmlns:wsu=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd\" wsu:Id=\"G1d58378c-e8cd-4bbd-be22-fcc5c4313558\">"
	    		+ "<wsse:Username>"+user+"</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">"+pass+"</wsse:Password>"
	     		+ "</wsse:UsernameToken></wsse:Security></soap:Header>";		

		try {
			// servicos que não necessitam de token
			
		    if (tipoTexte == SERVICO_SERVICO_COMPETENCIA){
                ret = execWS(URL_SERVICO_COMPETENCIA,header	+ "<soap:Body><con:obterCompetencia xmlns:con=\"http://competencia.ws.tce.sc.gov.br/\"><codigoUg>" + codUG + "</codigoUg></con:obterCompetencia></soap:Body></soap:Envelope>");
                return;
		    }

            if (tipoTexte == SERVICO_CONSULTA_ANTIGO_LISTARTabelasDisponiveis){
                ret = execWS(URL_SERVICO_CONSULTA_ANTIGO,header	+ "<soap:Body><con:listarTabelasDisponiveis xmlns:con=\"http://consulta.ws.tce.sc.gov.br/\"><competencia>"+competencia+"</competencia></con:listarTabelasDisponiveis></soap:Body></soap:Envelope>");
                return;
            }
            
            if (tipoTexte == SERVICO_CONSULTA_ANTIGO_LISTARRelatoriosDisponiveis){
                ret = execWS(URL_SERVICO_CONSULTA_ANTIGO,header	+ "<soap:Body><con:listarRelatoriosDisponiveis xmlns:con=\"http://consulta.ws.tce.sc.gov.br/\"><competencia>"+competencia+"</competencia></con:listarRelatoriosDisponiveis></soap:Body></soap:Envelope>");
                return;
            }
            
            
            if (tipoTexte == SERVICO_CONSULTA_ANTIGO_LISTAROperadoresFiltroConsultaDisponiveis){
                ret = execWS(URL_SERVICO_CONSULTA_ANTIGO,header	+ "<soap:Body><con:listarOperadoresFiltroConsultaDisponiveis xmlns:con=\"http://consulta.ws.tce.sc.gov.br/\"></con:listarOperadoresFiltroConsultaDisponiveis></soap:Body></soap:Envelope>");
                return;
            }
            if (tipoTexte == SERVICO_CONSULTA_ANTIGO_LISTARCamposPorTabela){
                ret = execWS(URL_SERVICO_CONSULTA_ANTIGO,header	+ "<soap:Body><con:listarCamposPorTabela xmlns:con=\"http://consulta.ws.tce.sc.gov.br/\"><identificadorTabTabela>"+ID_TABELA+"</identificadorTabTabela></con:listarCamposPorTabela></soap:Body></soap:Envelope>");
                return;
            }
            
            if (tipoTexte == SERVICO_MENSAGEM){
                ret = execWS(URL_SERVICO_MENSAGEM,header + "<soap:Body><con:listar xmlns:con=\"http://mensagem.ws.tce.sc.gov.br/\"/></soap:Body></soap:Envelope>");
                return;
            }
            
            
			// servicos que necessitam de token
			ret = execWS(TOKEN,header + "<soap:Body><ns2:obterToken xmlns:ns2=\"http://token.ws.tce.sc.gov.br/\"><codigoUg>" + codUG + "</codigoUg></ns2:obterToken></soap:Body></soap:Envelope>");
			try{
            	token = ret.substring(ret.indexOf(INICIO_TOKEN)+ INICIO_TOKEN.length(),ret.indexOf("</value>"));
            }catch (java.lang.StringIndexOutOfBoundsException e) {
            	tipoTexte = -1; // para não executar mais nehhum método
			}
            
            if ((tipoTexte == SERVICO_ENVIAR_ROLLBACK)||(tipoTexte == SERVICO_ENVIAR_COMMIT)){
            	ret = execWS(TOKEN,	header + "<soap:Body><ns2:iniciarTransferencia xmlns:ns2=\"http://token.ws.tce.sc.gov.br/\"><chaveToken>"+token+"</chaveToken></ns2:iniciarTransferencia></soap:Body></soap:Envelope>");
                ret = execWS(LANC_CONTAB_PU,header +  "<soap:Body><ns2:enviar xmlns:ns2=\"http://"+XMLNS_PREFIXO_SERVICO+".ws.tce.sc.gov.br/\"><chaveToken>"+token+"</chaveToken><competencia>"+competencia+"</competencia><lancamentoContabilPU><codigoProcessamento></codigoProcessamento><idRetorno>1</idRetorno><mensagemProcessamento></mensagemProcessamento><codigoContaContabil>222910200</codigoContaContabil><dataLancamento>2015-01-10</dataLancamento><historicoLancamento>historico</historicoLancamento><indicativoEstornoLancamento>S</indicativoEstornoLancamento><numeroControle>9555550</numeroControle><numeroSequencial>102</numeroSequencial><tipoLancamento>1</tipoLancamento><tipoMovimentoContabil>1</tipoMovimentoContabil><valorLancamento>555.55</valorLancamento></lancamentoContabilPU>"
      		             			+ "</ns2:enviar></soap:Body></soap:Envelope>");
            }
            
            if (tipoTexte == SERVICO_CONSULTA){
                ret = execWS(LANC_CONTAB_PU,header	+ "<soap:Body> <ns2:listar xmlns:ns2=\"http://"+XMLNS_PREFIXO_SERVICO+".ws.tce.sc.gov.br/\"><codigoUg>" + codUG + "</codigoUg><chaveToken>"+token+"</chaveToken><competencia>"+competencia+"</competencia><PAGINA>1</PAGINA></ns2:listar></soap:Body></soap:Envelope>");
            }

            if (tipoTexte == SERVICO_ARQUIVO_LISTAR){
                ret = execWS(URL_SERVICO_ARQ_FISICO,header + "<soap:Body> <ns2:listarArquivo xmlns:ns2=\"http://arquivofisico.ws.tce.sc.gov.br/\"> <chaveToken>"+token+"</chaveToken> <competencia>"+competencia+"</competencia> </ns2:listarArquivo></soap:Body> </soap:Envelope>");
            }

            
            if (tipoTexte == SERVICO_ARQUIVO_DOWNLOAD){
                ret = execWS(URL_SERVICO_ARQ_FISICO,header + "<soap:Body> <ns2:downloadArquivo xmlns:ns2=\"http://arquivofisico.ws.tce.sc.gov.br/\"> <chaveToken>"+token+"</chaveToken> <competencia>"+competencia+"</competencia> <nomeArquivo>asdasdas.txt</nomeArquivo></ns2:downloadArquivo></soap:Body> </soap:Envelope>");
            }
            
            if (tipoTexte == SERVICO_CONSULTA_ANTIGO_LISTAR){
                ret = execWS(URL_SERVICO_CONSULTA_ANTIGO,header	+ "<soap:Body> <ns2:listar xmlns:ns2=\"http://consulta.ws.tce.sc.gov.br/\"><codigoUg>" + codUG + "</codigoUg><competencia>"+competencia+"</competencia><identificadorTabTabela>"+ID_TABELA+"</identificadorTabTabela><pagina>1</pagina><chaveToken>"+token+"</chaveToken></ns2:listar></soap:Body></soap:Envelope>");
            }

            if (tipoTexte == SERVICO_CONSULTA_ANTIGO_LISTARRelatorio){
                ret = execWS(URL_SERVICO_CONSULTA_ANTIGO,header	+ "<soap:Body> <ns2:listarRelatorios xmlns:ns2=\"http://consulta.ws.tce.sc.gov.br/\"><codigoUg>" + codUG + "</codigoUg><competencia>"+competencia+"</competencia><identificadorRelatorio>"+ID_BALANCETE+"</identificadorRelatorio><chaveToken>"+token+"</chaveToken></ns2:listarRelatorios></soap:Body></soap:Envelope>");
            }
            
            if (tipoTexte == SERVICO_ARQUIVO_FISICO){
                ret = execWS(URL_SERVICO_ARQ_FISICO,header + "<soap:Body> <ns2:enviarArquivo xmlns:ns2=\"http://arquivofisico.ws.tce.sc.gov.br/\"> <chaveToken>"+token+"</chaveToken> <competencia>"+competencia+"</competencia> <arquivoFisico> <nomeArquivo>asdasdas.txt</nomeArquivo> <arquivo>UGFyYWLDqW5zDQoNClZvY8OqDQoNCkRlY29kaWZpY291IA0KDQpPIA0KDQpBcnF1aXZvDQoNCjop</arquivo> </arquivoFisico> </ns2:enviarArquivo></soap:Body> </soap:Envelope>");
            }

            if (tipoTexte == SERVICO_ENVIAR_COMMIT){
            	ret = execWS(TOKEN, header + "<soap:Body><ns2:finalizarTransferencia xmlns:ns2=\"http://token.ws.tce.sc.gov.br/\"><chaveToken>"+token+"</chaveToken></ns2:finalizarTransferencia></soap:Body></soap:Envelope>");
            }
            
            if (tipoTexte == SERVICO_ENVIAR_ROLLBACK){
            	ret = execWS(TOKEN,header + "<soap:Body><ns2:cancelarTransferencia xmlns:ns2=\"http://token.ws.tce.sc.gov.br/\"><chaveToken>"+token+"</chaveToken></ns2:cancelarTransferencia></soap:Body></soap:Envelope>");
            }
            
        } catch (Exception e) {
        	if (token != null){
        		try {
					ret = execWS(TOKEN,header + "<soap:Body><ns2:cancelarTransferencia xmlns:ns2=\"http://token.ws.tce.sc.gov.br/\"><chaveToken>"+token+"</chaveToken></ns2:cancelarTransferencia></soap:Body></soap:Envelope>");
				} catch (Exception e1) {
					e1.printStackTrace();
				}
        	}
        	if (urlConn != null){
        		urlConn.disconnect();
        	}
    		System.out.println("ERRO:" + e.getMessage());
		}
	}

	
	  
	private static String execWS(String url,String xml) throws IOException, MalformedURLException,
			ProtocolException, NoSuchAlgorithmException, KeyManagementException {

/*LOG*/	if (showHeaderLog){ System.out.println("--------------------------- Pacote: " + ++numPacote + " ----------------------------");};
/*LOG*/	if (showHeaderLog){ System.out.println("Serviço: " + url);};
		
		if (SERVER_BASE.toLowerCase().startsWith("https")){
/*LOG*/		if (showHeaderLog){ System.out.println("Ignorando Certificados");};
	        SSLContext ssl_ctx = SSLContext.getInstance("TLS");
	        TrustManager[ ] trust_mgr = get_trust_mgr();
	        ssl_ctx.init(null,                // key manager
	                     trust_mgr,           // trust manager
	                     new SecureRandom()); // random number generator
	        HttpsURLConnection.setDefaultSSLSocketFactory(ssl_ctx.getSocketFactory());

	        urlConn = (HttpsURLConnection)(new URL(url)).openConnection();

	        ((HttpsURLConnection)urlConn).setHostnameVerifier(new HostnameVerifier() {
	            public boolean verify(String host, SSLSession sess) {
	               return true;
	            }
	        });
		} else {
			urlConn = (HttpURLConnection) (new URL(url)).openConnection();
		}
		
		urlConn.setRequestMethod("POST");
		urlConn.setDoInput(true);
		urlConn.setDoOutput(true);
		if (usarGZIP){
			urlConn.setRequestProperty("Accept-Encoding", "gzip, deflate");
			urlConn.setRequestProperty("Content-encoding", "gzip");
	        urlConn.setRequestProperty("Content-type", "application/octet-stream");
		}else{
			urlConn.setRequestProperty("Content-Type", "charset=UTF-8");
		}

/*LOG*/	if (showHeaderLog){ System.out.println("CLIENTE->SERVIDOR");};
/*LOG*/	if (showWS_HeaderLog){ System.out.println("\t(HEADER) " + urlConn.getRequestProperties().toString());};


/*LOG*/	if (usarGZIP){
/*LOG*/		ByteArrayOutputStream wrParaLog =  new ByteArrayOutputStream();
/*LOG*/		GZIPOutputStream wrParaLogGZ =  new GZIPOutputStream(wrParaLog);
/*LOG*/		wrParaLogGZ.write((xml).getBytes("UTF-8"));
/*LOG*/		wrParaLogGZ.flush();
/*LOG*/		wrParaLogGZ.close();
/*LOG*/		if (showGZIPLog){ System.out.println("\t(XML GZIP)" + wrParaLog.toString());};
/*LOG*/}

/*LOG*/	if (showXMLPlanLog){  System.out.println("\t->(XML PLAN)"  + xml);};

		urlConn.connect();

		OutputStream wr = null;
		if (usarGZIP){
			wr = new GZIPOutputStream(urlConn.getOutputStream());
		}else{
			wr = urlConn.getOutputStream();
		}
		
		wr.write((xml).getBytes("UTF-8"));
		wr.flush();
		wr.close();
		
/*LOG*/	if (showHeaderLog){ System.out.println("SERVIDOR<-CLIENTE");};
/*LOG*/	if (showWS_HeaderLog){ System.out.println("\t(HEADER) " + urlConn.getHeaderFields().toString());};

		InputStream resultingInputStream = null;
		
		String encoding = urlConn.getContentEncoding();
		InputStream in = null;
		if (urlConn.getResponseCode() < 400) { 
			in = urlConn.getInputStream(); 
		} else { 
			in = urlConn.getErrorStream(); 
		} 
		
		if (encoding != null && encoding.equalsIgnoreCase("gzip")) {
			ByteArrayOutputStream pacoteGzip = readInputStreamAsString(in);
/*LOG*/		if (showGZIPLog){ System.out.println("\t(XML GZIP)" + pacoteGzip.toString());};
			resultingInputStream = new GZIPInputStream(new ByteArrayInputStream(pacoteGzip.toByteArray()));
		}else {
			resultingInputStream = in;
		}
		
		String response = readInputStreamAsString(resultingInputStream).toString();
  
		urlConn.disconnect();

/*LOG*/	if (showXMLPlanLog){  System.out.println("\t<-(XML PLAN)" + response); };
/*LOG*/	if (showHeaderLog){ System.out.println("");};
	
/*LOG*/	if (response.toLowerCase().contains("<status>ERRO</status>".toLowerCase())) { System.out.println("ERRO: " + response); }
		return response;
	}


	
	public static ByteArrayOutputStream readInputStreamAsString(InputStream in) 
		    throws IOException {

		    BufferedInputStream bis = new BufferedInputStream(in);
		    ByteArrayOutputStream buf = new ByteArrayOutputStream();

		    int result = bis.read();
		    while(result != -1) {
		      byte b = (byte)result;
		      buf.write(b);
		      result = bis.read();
		    }
		    return buf;
		}

	// para ignorar erros do certificado usado no servidor
	  private static TrustManager[ ] get_trust_mgr() {
	     TrustManager[ ] certs = new TrustManager[ ] {
	        new X509TrustManager() {
	           public X509Certificate[ ] getAcceptedIssuers() { return null; }
	           public void checkClientTrusted(X509Certificate[ ] certs, String t) { }
	           public void checkServerTrusted(X509Certificate[ ] certs, String t) { }
	         }
	      };
	      return certs;
	  }
	
}
