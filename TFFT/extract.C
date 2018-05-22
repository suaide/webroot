void extract(char* rootfile, char* datafile, char* hist)
{
  TFile *F = new TFile(rootfile);
  
  TH1D* H = (TH1D*)F->Get(hist);    
  ofstream file(datafile);

  file << "<?php"<<endl;
  
  if(!H)
  {
    file << "   $tabela_ok     = false;"<<endl;
    file << "?>"<<endl;
    file.close();
    return;
  }
  
  file << "   $tabela_ok     =  true;"<<endl;
  
  int N = H->GetNbinsX();
  
  file << "   $data[\"VERSION\"] = 1;"<<endl;
  file << "   $data[\"NC\"] = 2;"<<endl;
  file << "   $data[\"NL\"] = "<<N<<";"<<endl;
  file << "   $NOME[0]      = \""<<H->GetXaxis()->GetTitle()<<"\";"<<endl;
  file << "   $NOME[1]      = \""<<H->GetYaxis()->GetTitle()<<"\";"<<endl;
  file << "   $TITLE        = \""<<H->GetTitle()<<"\";"<<endl;
  
  for(int i = 1; i<=N; i++)
  {
    double x = H->GetBinCenter(i);
    double y = H->GetBinContent(i);
    double c = i-1;
    
    file << "   $data[\"R"<<c<<"C0\"] = "<<x<<";"<<endl;
    file << "   $data[\"R"<<c<<"C1\"] = "<<y<<";"<<endl;
  }
  file << "  "<<endl;
  file << "?>"<<endl;
  file.close();
  return;
}