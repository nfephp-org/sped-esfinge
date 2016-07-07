package teste;
/**
 * Class Description : 
 * 
 * Project : WebService eSfinge Obras
 * @author : Sandro Daros De Luca - sandroluca@tce.sc.gov.br
 * Date Created : 09/11/2008
 * @version : 1.1
 * Date release : 01/12/2008
 */

import java.rmi.RemoteException;
import javax.xml.namespace.QName;
import javax.xml.rpc.ServiceException;
import org.apache.axis.client.Call;
import org.apache.axis.client.Service;

public class ClienteExemplo {

	private static final String HTTP_WS_ESFINGE_OBRAS = "http://desenv.tce.sc.gov.br:8080/axis/WSeO.jws";
	
	/**
	 * 09/11/2008
	 * Inicializa o serviço do WebService client
	 * @author Sandro Daros De Luca
	 */
	private static Call initWS(String metodo) throws ServiceException {
		String endpoint = HTTP_WS_ESFINGE_OBRAS;
		Service service = new Service();
		Call call;
		call = (Call) service.createCall();
		call.setTargetEndpointAddress(endpoint);
		System.setProperty("entityExpansionLimit", "50000000");
		call.setTimeout(new Integer(50000000));
		call.setOperationName(new QName(endpoint, metodo));
		return call;
	}

	/**
	 * 09/11/2008
	 * Teste o serviço "echo", envia a string "teste" e escreve a resposta na tela. 
	 * @author Sandro Daros De Luca
	 */
	private static void callEcho() {
		Call call;
		String ret = "";
		try {
			call = initWS("echo");
			ret = (String) call.invoke(new Object[] { new String("teste") });
			System.out.println(ret);
		} catch (RemoteException e) {
			e.printStackTrace();
		} catch (ServiceException e) {
			e.printStackTrace();
		}
	}

	/**
	 * 09/11/2008
	 * Executa um serviço qualquer com 3 parâmetros (usuário, senha e XML de objetos) e escreve a resposta na tela.
	 * @author Sandro Daros De Luca
	 */
	public static void chamaMetodo(String metodo, String usuario, String senha,
			String parametro) {
		Call call;
		String ret = "";
		try {
			call = initWS(metodo);
			ret = (String) call.invoke(new Object[] { new String(usuario),
					new String(senha), new String(parametro) });
		} catch (RemoteException e) {
			e.printStackTrace();
		} catch (ServiceException e) {
			e.printStackTrace();
		}
		System.out.println(ret);
	}

	/**
	 * 09/11/2008
	 * Executa um serviço qualquer com 5 parâmetros (usuário, senha e XML de objetos, listarNulos, listarCompetencia) e escreve a resposta na tela.
	 * @author Sandro Daros De Luca
	 */
	public static void chamaMetodo(String metodo, String usuario, String senha,
			String parametro,boolean listarNulos,boolean listarCompetencia) {
		Call call;
		String ret = "";
		try {
			call = initWS(metodo);
			ret = (String) call.invoke(new Object[] { new String(usuario),
					new String(senha), new String(parametro), new Boolean(listarNulos),new Boolean(listarCompetencia)});
		} catch (RemoteException e) {
			e.printStackTrace();
		} catch (ServiceException e) {
			e.printStackTrace();
		}
		System.out.println(ret);
	}
	
	
	/**
	 * 20/11/2008
	 * Executa um serviço qualquer com 2 parâmetros (usuário, senha) e escreve a resposta na tela.
	 * @author Sandro Daros De Luca
	 */
	public static void chamaMetodo(String metodo, String usuario, String senha) {
		Call call;
		String ret = "";
		try {
			call = initWS(metodo);
			ret = (String) call.invoke(new Object[] { new String(usuario),
					new String(senha)});
		} catch (RemoteException e) {
			e.printStackTrace();
		} catch (ServiceException e) {
			e.printStackTrace();
		}
		System.out.println(ret);
	}

	public static void main(String[] args) {

		// chama o método list para listar todas as Licitações do usuário "teste" senha "123456"
		chamaMetodo("list", "teste", "123456",
				"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>" 
				+ "<list>"
						+ "<objetos>" 
							+ "<Licitacao>" 
							+ "</Licitacao>"
						+ "</objetos>" 
				+ "</list>");

		/* chama o método list listando dois objetos específicos (filtrando pelo identificador)
		 * do usuário "teste" senha "123456"
		 */
		chamaMetodo("list", "teste", "123456",
				"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>" 
				+ "<list>"
					+ "<objetos>" 
						+ "<Contratado>"
							+ "<identificador>3813</identificador>"
						+ "</Contratado>"
					+ "</objetos>" 
					+ "<objetos>" 
						+ "<AcompanhamentoContrato>"
							+ "<identificador>58844</identificador>"
						+ "</AcompanhamentoContrato>"
					+ "</objetos>" 
				+ "</list>",true,true);		
		
		
		// chama o método que testa a chamada o echo
		callEcho();
		
		// chama o método para obter a competência corrente
		chamaMetodo("getCompetenciaCorrrente", "teste", "123456");
	}
}