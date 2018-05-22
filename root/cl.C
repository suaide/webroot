TF1 *f = 0;
TF1 *P = 0;
double xmin = 0;
double xmax = 0;

double F2(double *x, double *par) 
{ 
  double x1 = f->GetXmin();
  double x2 = f->GetXmax();
  f->SetRange(xmin,xmax);
  double integral =  f->Integral(xmin,x[0]);
  f->SetRange(x1,x2);
  return integral;
}

void cl(int mode = 0, int p0 = 3, double p1 = 4, double p2 = 3, double p3 = 0, 
        double Pinf = 0.25, double Psup = 0.6,
        char* name = "cl")
{
  
  gROOT->SetStyle("Modern");
  char *eixox = "";
  if(mode == 0) // distribuição de chi2
  {
    // p0 = numero de graus de liberdade
    
    xmin = 0;
    xmax = p0*100;
    if(p0==1) xmin = 0.0001;
    
    f = new TF1("f","(1/(pow(2,[0]/2)*TMath::Gamma([0]/2)))*pow(x,[0]/2-1)*exp(-x/2)",xmin,p0*5);
    eixox = "#chi^{2}";
  }
  if(mode == 1) // distribuição de chi2 - red
  {
    // p0 = numero de graus de liberdade
    
    f = new TF1("f","(1*[0]/(pow(2,[0]/2)*TMath::Gamma([0]/2)))*pow(x*[0],[0]/2-1)*exp(-x*[0]/2)",0,5);
    xmin = 0;
    xmax = 100;
    eixox = "#chi^{2}_{red}";
  }
  if(mode == 2) // distribuição gaussiana
  {
    // p1 = media
    // p2 = desvio padrao
    
    f = new TF1("f","1/(sqrt(2*TMath::Pi())*[2])*exp(-0.5*pow((x-[1])/[2],2))",p1-7*p2,p1+7*p2);
    xmin = p1-40*p2;
    xmax = p1+40*p2;
    eixox = "x";

  }
  if(mode == 3) // distribuição de Student
  {
    // p0 = numero de graus de liberdade
    // p1 = media
    // p2 = desvio padrao
    

    double scale = 7+5/p0;
    //scale = 1500;
    
    f = new TF1("f","TMath::Gamma(([0]+1)/2)/(TMath::Gamma([0]/2)*sqrt(TMath::Pi()*[0])*[2])*pow((1+1/[0]*pow((x-[1])/[2],2)),-([0]+1)/2)",p1-scale*p2,p1+scale*p2);
    scale = 100;
    if(p0==1) scale = 500000;
    if(p0==2) scale = 10000;
    if(p0==3) scale = 1000;
    xmin = p1-scale*p2;
    xmax = p1+scale*p2;
    eixox = "x";

  }
  
  
  P = new TF1("P",F2,xmin,xmax,1);
  
  f->SetParameter(0,fabs((double)p0));
  f->SetParameter(1,p1);
  f->SetParameter(2,p2);
  f->SetParameter(3,p3);  
  
  float Xinf = P->GetX(Pinf);
  float Xsup = P->GetX(Psup);
  
  c = new TCanvas("c","",1200,800);
  c->Divide(2,1);
  
  f->SetNpx(1000);
  P->SetNpx(1000);

  c->cd(1);  
  f->Draw();
  f->SetLineColor(2);
  f->SetLineWidth(2);
  f->GetXaxis()->SetTitle(eixox);
  f->SetTitle("F.D.P.");
  
  TF1 *ffil = new TF1(*f);
  ffil->SetRange(Xinf,Xsup);
  ffil->Draw("Fsame");
  ffil->SetFillColor(4);
  ffil->SetFillStyle(3004);
  
  c->cd(2);  
  
  P->SetRange(f->GetXmin(),f->GetXmax());
  P->Draw();
  P->SetLineColor(2);
  P->SetLineWidth(2);
  P->GetXaxis()->SetTitle(eixox);
  P->SetTitle(Form("P(valor < %s )",eixox));

  TF1 *Pfil = new TF1(*P);
  Pfil->SetRange(Xinf,Xsup);
  Pfil->Draw("Fsame");
  Pfil->SetFillColor(4);
  Pfil->SetFillStyle(3004);
  
  c->Print(Form("%s.eps",name));
  
  ofstream file(Form("%s.php",name));
  
  file << "<?php"<<endl;
  file << "  $CONF_MIN = "<<(double)((int)(Xinf*1000))/1000<<";"<<endl;
  file << "  $CONF_MAX = "<<(double)((int)(Xsup*1000))/1000<<";"<<endl;
  file << "?>"<<endl;
  
  file.close();
  
  cout <<"Pronto"<<endl;

    
}
