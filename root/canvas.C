TList* l;
void divide_canvas_vert(TCanvas* c,double inf = 0.3)
{
  c->Divide(1,2);
  c->cd(1);
  gPad->SetPad(0,inf,1,1);
  gPad->SetBottomMargin(0);
  gPad->SetTopMargin(0.1);
  
  c->cd(2);
  gPad->SetPad(0,0,1,inf);
  gPad->SetTopMargin(0);
  gPad->SetBottomMargin(0.2);
  
  c->Modified();
  c->Update();
  
}

void canvas()
{
  c = new TCanvas();
  h = new TH2F("h","teste",100,2.45,8.45,100,-2.5,5.5);
  
  divide_canvas_vert(c,0.3);
  c->cd(1); h->Draw();
  c->cd(2); h->Draw();
  
  //h->Draw();
  
  c->Modified();
  c->Update();
  
  l = c->GetListOfPrimitives();
  l->ls();
  int n = l->GetEntries();
  for(int i=0;i<n;i++)
  {
    TObject *o = l->At(i);
    if(!strcmp(o->ClassName(),"TPad"))
    {
      cout <<"\n\nAchei Pad"<<endl;
      TPad *p = (TPad*)o;
      p->cd();
      cout <<"XlowNDC = "<<p->GetXlowNDC()<<"  WNDC = "<<p->GetWNDC()<<"    YlowNDC = "<<p->GetYlowNDC()<<"  HNDC = "<<p->GetHNDC()<<endl;
      cout <<"AbsXlowNDC = "<<p->GetAbsXlowNDC()<<"  AbsWNDC = "<<p->GetAbsWNDC()<<"    AbsYlowNDC = "<<p->GetAbsYlowNDC()<<"  AbsHNDC = "<<p->GetAbsHNDC()<<endl;
      cout <<"X1 = "<<p->GetX1()<<"  X2 = "<<p->GetX2()<<"    Y1 = "<<p->GetY1()<<"  Y2 = "<<p->GetY2()<<endl;
      cout <<"Uxmin = "<<p->GetUxmin()<<"  Uxmax = "<<p->GetUxmax()<<"    Uymin = "<<p->GetUymin()<<"  Uymax = "<<p->GetUymax()<<endl;
      cout <<"Xmin = "<<p->AbsPixeltoX(p->UtoAbsPixel(0))<<"  Xmax = "<<p->AbsPixeltoX(p->UtoAbsPixel(1))<<"  Ymin = "<<p->AbsPixeltoY(p->VtoAbsPixel(0))<<"  Ymax = "<<p->AbsPixeltoY(p->VtoAbsPixel(1))<<endl;
    }
  }
  
}