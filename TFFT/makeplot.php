
<?php

function makeplot($DADOS,$ROOTDIR,$TMPDIR, $ROOTSTYLE)
{
  $tmp = $TMPDIR."/".$DADOS["SESSION_ID"].".C";
  $base = $TMPDIR."/".$DADOS["SESSION_ID"];
  $arq = $TMPDIR."/".$DADOS["SESSION_ID"].".png";
  $val = $TMPDIR."/".$DADOS["SESSION_ID"].".php";
  $rootfile = $TMPDIR."/".$DADOS["SESSION_ID"].".root";
  $rootfile_1 = $TMPDIR."/".$DADOS["SESSION_ID"]."_1.root";
  
  $NG = 0;
  if(isset($DADOS["fft_signal"])) $NG++;
  if(isset($DADOS["fft_mag"])) $NG++;
  if(isset($DADOS["fft_fase"])) $NG++;
  if(isset($DADOS["fft_mag_filt"])) $NG++;
  if(isset($DADOS["fft_fase_filt"])) $NG++;
  if(isset($DADOS["fft_inversa"])) $NG++;
  if(isset($DADOS["desenha_filtro_MAG"])) $NG++;
  if(isset($DADOS["desenha_filtro_FASE"])) $NG++;
  
  if($NG==0) return;
  
  $W = $DADOS["LARGURA"];
  $H = $DADOS["ALTURA"]*$NG;
  $FUNDO = "0";
  $FRENTE = "1";
  $FONTE = "132";
  
  $f = fopen($tmp,"w");
  
  $TOP = false;
  $CD = 1;
  
  fwrite($f,'void '.$DADOS["SESSION_ID"].'()
{    
  '.style($ROOTSTYLE,$FUNDO,$FRENTE,$FONTE).'    
  c = new TCanvas("c","c",'.$W.','.$H.'); 
  c->Divide(1,'.$NG.');
  TFile *F = new TFile("'.$rootfile.'");
  TFile *F_1 = new TFile("'.$rootfile_1.'","RECREATE");
  
  F->cd();
  
  TH1D* HIST = (TH1D*)F->Get("MAG_axis");;
  TF1 *filtro_mag  = 0;
  TF1 *filtro_fase = 0;
  
  double freq_min = HIST->GetBinCenter(1);
  double freq_max = HIST->GetBinCenter(HIST->GetNbinsX());
    
');
  if($DADOS["freq_min_FFT"]!="" && $DADOS["freq_max_FFT"]!="")
  {
    if(is_numeric($DADOS["freq_min_FFT"]) && is_numeric($DADOS["freq_max_FFT"]))
    {
      $MINFIT = floatval($DADOS["freq_min_FFT"]);
      $MAXFIT = floatval($DADOS["freq_max_FFT"]);
      fwrite($f,'freq_min = '.$MINFIT.';
                 freq_max = '.$MAXFIT.'; ');
    }     
  }
  if($DADOS["filtro_MAG"]!="")
  {
    $FUNC = $DADOS["filtro_MAG"];
    fwrite($f,'filtro_mag = new TF1("filtro_mag","'.$FUNC.'",0,1); ');
    if($DADOS["par_MAG"]!="") fwrite($f,'  filtro_mag->SetParameters('.htmlspecialchars($DADOS["par_MAG"]).',0); ');
  }
  else
  {
    fwrite($f,'filtro_mag = new TF1("filtro_mag","1",0,1); ');
  }  
  
  if($DADOS["filtro_FASE"]!="")
  {
    $FUNC = $DADOS["filtro_FASE"];
    fwrite($f,'filtro_fase = new TF1("filtro_fase","'.$FUNC.'",0,1); ');
    if($DADOS["par_FASE"]!="") fwrite($f,'  filtro_fase->SetParameters('.htmlspecialchars($DADOS["par_FASE"]).',0); ');
  }
  else
  {
    fwrite($f,'filtro_fase = new TF1("filtro_fase","0",0,1); ');
  }  

  if(isset($DADOS["fft_signal"]))
  {
    fwrite($f,'
    c->cd('.$CD.');
    HIST = (TH1D*)F->Get("sinal");
    HIST->GetXaxis()->SetTitle("x [u]");
    HIST->GetYaxis()->SetTitle("Magnitude");
    HIST->Draw();
    F_1->cd();
    HIST->Write();
    F->cd();
    '); 
    $CD++;
  }
  
  if(isset($DADOS["fft_inversa"]))
  {
    fwrite($f,'
    c->cd('.$CD.');
    TH1D *MAG = (TH1D*)F->Get("MAG");
    TH1D *MAG_AXIS = (TH1D*)F->Get("MAG_axis");
    TH1D *FAS = (TH1D*)F->Get("FASE");
    int N = MAG->GetNbinsX();
    
    double *RE = new double[N];
    double *IM = new double[N];
    
    for(int i = 0; i<N; i++)
    {
      double mag = MAG->GetBinContent(i+1);
      double fas = FAS->GetBinContent(i+1);
      
      double index = 0;
      if(i<N/2) index = i+N/2;
      else index = i-N/2-1;
      
      double freq = MAG_AXIS->GetBinCenter(index);
      
      mag*=filtro_mag->Eval(freq);
      if(freq>=0) fas+=filtro_fase->Eval(freq);
      else fas+=(-filtro_fase->Eval(freq));
      
      
      RE[i] = mag*cos(fas)/N;
      IM[i] = mag*sin(fas)/N;
    }
    
    HIST = (TH1D*)F->Get("sinal");
    TH1D *INV = new TH1D(*HIST);
    INV->SetName("sinal_inv");
    
    TVirtualFFT *fft_back = TVirtualFFT::FFT(1, &N, "C2R M K");
    fft_back->SetPointsComplex(RE,IM);
    fft_back->Transform();
    TH1 *hb = 0;
    // Lets look at the output
    hb = TH1::TransformHisto(fft_back,hb,"Re");
   
    for(int i = 0; i<N; i++) INV->SetBinContent(i+1,hb->GetBinContent(i+1));
    INV->SetTitle("Inversa da FFT filtrada");
    INV->Draw();
    
    F_1->cd();
    INV->Write();
    F->cd();
   
    '); 
    $CD++;
  }

  if(isset($DADOS["fft_mag"]))
  {
    fwrite($f,'
    c->cd('.$CD.');
    HIST = (TH1D*)F->Get("MAG_axis");
    HIST->GetXaxis()->SetTitle("f_{x} [1/u]");
    HIST->GetYaxis()->SetTitle("Magnitude");
    HIST->Draw();
    HIST->GetXaxis()->SetRangeUser(freq_min,freq_max);
    F_1->cd();
    HIST->Write();
    F->cd();

    '); 
    $CD++;
  }
  
  if(isset($DADOS["fft_mag_filt"]))
  {
    fwrite($f,'
    c->cd('.$CD.');
    HIST = (TH1D*)F->Get("MAG_axis");
    TH1D *MF = new TH1D(*HIST);
    MF->SetName("MAG_axis_filt");
    MF->SetTitle("Magnitude da FFT filtrada");
    MF->GetXaxis()->SetTitle("f_{x} [1/u]");
    MF->GetYaxis()->SetTitle("Magnitude");
    int N = MF->GetNbinsX();
    for(int i=1;i<=N;i++)
    {
      double freq = fabs(MF->GetBinCenter(i));
      MF->SetBinContent(i,MF->GetBinContent(i)*filtro_mag->Eval(freq));
    }
    MF->Draw();
    MF->GetXaxis()->SetRangeUser(freq_min,freq_max);
    F_1->cd();
    MF->Write();
    F->cd();

    '); 
    $CD++;
  }

  
  if(isset($DADOS["fft_fase"]))
  {
    fwrite($f,'
    c->cd('.$CD.');
    HIST = (TH1D*)F->Get("FASE_axis");
    HIST->GetXaxis()->SetTitle("f_{x} [1/u]");
    HIST->GetYaxis()->SetTitle("Fase");
    HIST->Draw();
    HIST->GetXaxis()->SetRangeUser(freq_min,freq_max);
    F_1->cd();
    HIST->Write();
    F->cd();
    '); 
    $CD++;
  }
  
  
  if(isset($DADOS["fft_fase_filt"]))
  {
    fwrite($f,'
    c->cd('.$CD.');
    HIST = (TH1D*)F->Get("FASE_axis");
    TH1D *MF = new TH1D(*HIST);
    MF->SetName("FASE_axis_filt");
    MF->SetTitle("Fase da FFT filtrada");
    MF->GetXaxis()->SetTitle("f_{x} [1/u]");
    MF->GetYaxis()->SetTitle("Fase");
    int N = MF->GetNbinsX();
    for(int i=1;i<=N;i++)
    {
      double freq = fabs(MF->GetBinCenter(i));
      if(freq>=0) MF->SetBinContent(i,MF->GetBinContent(i)+filtro_fase->Eval(freq));
      else MF->SetBinContent(i,MF->GetBinContent(i)-filtro_fase->Eval(freq));
    }
    MF->Draw();
    MF->GetXaxis()->SetRangeUser(freq_min,freq_max);
    F_1->cd();
    MF->Write();
    F->cd();

    '); 
    $CD++;
  }
  
  fwrite($f,estatisticas_grafico($arq,$val,$W,$H,"c",false,"f")); 
  fwrite($f,' F->Close(); F_1->Close(); return; } ');
  
  fclose($f);
  $error = executa($ROOTDIR."/bin/root -b -q ".$tmp,$DADOS["SESSION_ID"],$TMPDIR);
  if ($error!=0) return "ERROR";
  return $arq;
  
}  

?>
