void fft(char* input, char* output, char* tmp)
{
  gSystem->Load("libGraf");  
  TGraph *G = new TGraph(input,"%lg %lg");
  int n = G->GetN()-1;
  double *X = G->GetX();
  double *Y = G->GetY();
    
  TH1D *H = new TH1D("sinal","Sinal de entrada", n,X);
  for(int i = 1; i<=n; i++) H->SetBinContent(i,Y[i-1]);
  
  TH1 *hm =0;
  TVirtualFFT::SetTransform(0);
  hm = H->FFT(hm, "MAG");
  hm->SetTitle("Magnitude da FFT");
  hm->SetName("MAG");
  hm->GetXaxis()->SetTitle("freq");
  //hm->Scale(1/sqrt(double(n)));

  TH1 *hp = 0;
  hp = H->FFT(hp, "PH");
  hp->SetTitle("Fase da FFT");
  hp->SetName("FASE");
  hp->GetXaxis()->SetTitle("freq");
  
  double xmin = hm->GetBinLowEdge(1)/(X[n]-X[0]);
  double xmax = hm->GetBinLowEdge(hm->GetNbinsX()+1)/(X[n]-X[0]);
  TH1D* hms = new TH1D("MAG_axis","Magnitude da FFT",n,-xmax/2,xmax/2);
  TH1D* hps = new TH1D("FASE_axis","Fase da FFT",n,-xmax/2,xmax/2);

  hms->GetXaxis()->SetTitle("freq");
  hps->GetXaxis()->SetTitle("freq");
    
  for(int i = 1;i<=n;i++)
  {
    if(i<n/2)
    {
      hms->SetBinContent(i+n/2,hm->GetBinContent(i));
      hps->SetBinContent(i+n/2,hp->GetBinContent(i));
    }
    else
    {
      hms->SetBinContent(i-n/2-1,hm->GetBinContent(i));
      hps->SetBinContent(i-n/2-1,hp->GetBinContent(i));      
    }
  }
  
  double bin_max = hms->GetMaximumBin();
  double f_max = fabs(hms->GetBinCenter(bin_max));
  hms->Scale(1/sqrt(double(n)));
  
  TFile *f = new TFile(output,"RECREATE");
  H->Write();
  hm->Write();
  hms->Write();
  hp->Write();
  hps->Write();
  f->Close();
  
  double range_freq = xmax/2;
  ofstream file(tmp);

  file << "<?php"<<endl;
  file << "   $_POST[\"range_freq\"]     = \""<<range_freq<<"\";"<<endl;
  file << "   $_POST[\"max_freq\"]     = \""<<f_max<<"\";"<<endl;
  file << "  "<<endl;
  file << "?>"<<endl;

  
}